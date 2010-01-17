<?php
/*
    Tatoeba Project, free collaborative creation of multilingual corpuses project
    Copyright (C) 2009  HO Ngoc Phuong Trang <tranglich@gmail.com>

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


// navigation (previous, random, next)
$navigation->displaySentenceNavigation();
?>

<div id="annexe_content">
	
	<div class="module">
		<?php
		echo '<h2>';
		__('Logs');
		echo '</h2>';
		
		//$contributions = $sentence['Contribution'];
		if(count($contributions) > 0){
			echo '<div id="logs">';
			foreach($contributions as $contribution){
                $logs->annexeEntry($contribution['Contribution'], $contribution['User']);
			}
			echo '</div>';
		}else{
			echo '<em>'. __('There is no log for this sentence', true) .'</em>';
		}
		?>
	</div>	
	<div class="module">
	    <h2><?php __('Notify mistakes') ?></h2>
	    <p>
            <?php
            __('Do not hesitate to post a comment if you see a mistake!');
            ?>
        </p>
        <p>
            <?php
            __('NOTE : If the sentence does not belong to anyone and you know how to correct the mistake, feel free to correct it without posting any comment. You will have to adopt the sentence before you can edit it.');
            ?>
        </p>
	</div>
</div>

<div id="main_content">
	<div class="module">
		<?php
		if($sentence != null){
			echo '<h2>' .
                    sprintf ( __('Sentence nº%s', true) , $sentence['Sentence']['id']) .
                '</h2>';
			$this->pageTitle = __('Example sentence: ',true) . $sentence['Sentence']['text'];

            // /!\ the id is used in sentences.show.js , so do not touch the way we forge the id /!\ 
			echo '<div id="__'.$sentence['Sentence']['id'] . '" class="sentences_set">';
				// sentence menu (translate, edit, comment, etc)
                // TODO set up a better mechanism
				$specialOptions['belongsTo'] = $sentence['User']['username']; 
				$sentences->displayMenu(
                    $sentence['Sentence']['id'],
                    $sentence['Sentence']['lang'],
                    $specialOptions
                );

				

                // for edit in place...
				// TODO set up a better mechanism
				$sentence['User']['canEdit'] = $specialOptions['canEdit']; 
				
				// display sentence and translations
				$sentences->displayGroup(
                    $sentence['Sentence'],
                    $translations,
                    $sentence['User'],
                    $indirectTranslations,
					true // so that the sentence is a div and not a link
                );
			echo '</div>';

			//$tooltip->displayAdoptTooltip();
            // TODO link with sentences.show.js
            $javascript->link('sentences.show.js',false);
            

		}else{
			$this->pageTitle = __('Sentence does not exist: ', true) . $this->params['pass'][0];
			
			echo '<h2>' .
                sprintf (__('Sentence nº%s', true), $this->params['pass'][0]) .
                '</h2>';

			echo '<div class="error">';
                echo sprintf(__('There is no sentence with id %s',true), $this->params['pass'][0]);
			echo '</div>';
		}
		?>
	</div>

	<div class="module">
		<?php
        /*
		echo '<div class="addComment">';
		echo $html->link(
			__('Add a comment',true),
			array("controller" => "sentence_comments", "action" => "show", $sentence['Sentence']['id'].'#add_comment')
		);
		echo '</div>';			
		*/
		echo '<h2>';
		__('Comments');
		echo '</h2>';

		if(count($sentenceComments) > 0){
			echo '<ol class="comments">';
            // always do count outside the loop
            // it's useless to call this function each iteration
            $numberOfComments = count($sentenceComments);
			for($i = 0; $i < $numberOfComments ; $i++){
				$comment = $sentenceComments[$i];
				$comments->displaySentenceComment($comment);
			}
			echo '</ol>';
		
            // TODO remove this 
            /*	
			if(count($sentenceComments) > 3){
				?>
				<p class="more_link">
				<?=$html->link(
					__('See all comments',true),
					array(
						"controller" => "sentence_comments",
						"action" => "show",
						$sentence['Sentence']['id']
					)); 
				?>
				</p>
				<?php
			}*/
		}else{
			echo '<em>' . __('There are no comments for now.', true) .'</em>';
		}
		?>
		
		
	</div>
    <div class="module">
	<?php
		if($sentenceExists){
			echo '<a name="add_comment"></a>';
			echo '<h2>';
			__('Add a comment');
			echo '</h2>';
			if($session->read('Auth.User.id')){
				$comments->displayCommentForm($sentence['Sentence']['id'], $sentence['Sentence']['text']);
			}else{
				echo '<p>';
				echo sprintf(
					__('You need to be logged in to add a comment. If you are not registered, you can <a href="%s">register here</a>.',true),
					$html->url(array("controller"=>"users", "action"=>"register"))
				);
				echo '</p>';
			}
		}
		?>
	</div>

</div>

