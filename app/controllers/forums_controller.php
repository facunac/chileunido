<?php

class ForumsController extends AppController {
    var $name = 'Forums';
    var $uses = array("Phpbb_posts_text","Phpbb_posts");

    function index($id=null) {
        $this->escribirHeader("Foro");
        if($id==null) {
            $this->set('direccion','/phpBB2/index.php');
        }
        else {
            $posts=$this->Phpbb_posts->findAll();
            foreach ($posts as $phpbb_post) {
                if($phpbb_post['Phpbb_posts']['post_id']==$id)
                {
                    $this->set('direccion','/phpBB2/viewtopic.php?t='.$phpbb_post['Phpbb_posts']['topic_id']);
                }
            }
        }

    }

    function mostrarInicio() {
        $this->set('Phpbb_posts_texts', $this->Phpbb_posts_text->findAll(null,null,"post_id DESC",null,1,null));
    }
}

?>
