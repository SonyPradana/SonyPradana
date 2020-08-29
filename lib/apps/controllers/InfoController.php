<?php

class InfoController extends Controller{
    public function render($article_name){
        // portal load di view page-nya
        return $this->view($article_name);
    }
}
