<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SentenceCommentsTable;
use Cake\TestSuite\TestCase;
use Cake\ORM\TableRegistry;
use Cake\Event\Event;
use Cake\Core\Configure;
use App\Model\CurrentUser;

class SentenceCommentTest extends TestCase {

    public $fixtures = array(
        'app.sentence_comments',
        'app.users_languages'
    );

    public function setUp() {
        parent::setUp();
        Configure::write('Acl.database', 'test');
        $this->SentenceComment = TableRegistry::getTableLocator()->get('SentenceComments');
    }

    public function tearDown() {
        unset($this->SentenceComment);

        parent::tearDown();
    }

    public function testSave_newCommentfiresEvent() {
        $comment = $this->SentenceComment->newEntity([
            'sentence_id' => 1,
            'text' => 'What a great sentence!',
            'user_id' => 4,
        ]);

        $dispatched = false;
        $model = $this->SentenceComment;
        $model->getEventManager()->on(
            'Model.SentenceComment.commentPosted',
            function (Event $event) use ($model, &$dispatched, $comment) {
                $this->assertSame($model, $event->getSubject());
                $this->assertEquals($comment, $event->getData('comment'));
                $dispatched = true;
            }
        );

        $this->SentenceComment->save($comment);

        $this->assertTrue($dispatched);
    }

    public function testSave_savesNewComment() {
        $comment = $this->SentenceComment->newEntity([
            'sentence_id' => 1,
            'text' => 'What a great sentence!',
            'user_id' => 4,
        ]);

        $saved = $this->SentenceComment->save($comment);

        $this->assertTrue((bool)$saved);
    }

    public function testSave_failsSavingNewEmptyComment() {
        $comment = $this->SentenceComment->newEntity([
            'sentence_id' => 1,
            'text' => ' ',
            'user_id' => 4,
        ]);

        $saved = $this->SentenceComment->save($comment);

        $this->assertFalse((bool)$saved);
    }

    public function testSave_editsExistingComment() {
        $comment = $this->SentenceComment->get(2);
        $this->SentenceComment->patchEntity(
            $comment,
            ['text' => 'Someone should REALLY delete this sentence.']
        );
        $saved = $this->SentenceComment->save($comment);

        $this->assertTrue((bool)$saved);
    }

    public function testSave_failsEditingCommentWithEmptyContents() {
        $comment = $this->SentenceComment->get(2);
        $this->SentenceComment->patchEntity($comment, ['text' => ' ']);
        $saved = $this->SentenceComment->save($comment);

        $this->assertFalse((bool)$saved);
    }

    public function testSave_hidingMessageDoesNotUpdateLastModifiedField() {
        $messageId = 2;
        $message = $this->SentenceComment->get($messageId);
        $before = $message->modified;
        $message->hidden = true;
        $this->SentenceComment->save($message);
        $after = $this->SentenceComment->get($messageId, ['fields' => ['modified']])->modified;

        $this->assertEquals($before, $after);
    }

    public function testDeleteComment_succeeds() {
        CurrentUser::store(['id' => 3]);
        $result = $this->SentenceComment->deleteComment(2);
        $this->assertTrue($result);
    }

    public function testDeleteComment_failsWrongId() {
        CurrentUser::store(['id' => 3]);
        $result = $this->SentenceComment->deleteComment(99999);
        $this->assertFalse($result);
    }

    public function testDeleteComment_failsBecauseNotAllowed() {
        CurrentUser::store(['id' => 3]);
        $result = $this->SentenceComment->deleteComment(1);
        $this->assertFalse($result);
    }
}