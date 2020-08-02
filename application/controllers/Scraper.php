<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Scraper extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }
   
    public function get_lattest()
    {
		$html = file_get_html('https://mangakakalot.com/latest');
		$res = array();
		foreach($html->find('div[class=list-truyen-item-wrap]') as $e) {
			$data = array(
				'img' => $e->find('img',0)->src,
				'title' => $e->find('h3',0)->plaintext,
				'mangauri' => $e->find('h3,a',0)->href,
				'chapter' => $e->find('.list-story-item-wrap-chapter',0)->plaintext,
				'chapteruri' => $e->find('.list-story-item-wrap-chapter',0)->href,
				'desc' => $e->find('p',0)->plaintext
			);
			
			array_push($res, $data);
		}
		
		header('Content-type: application/json; charset=utf-8');
		echo json_encode($res);
    }
}
