<?php
  defined('BASEPATH') OR exit('No direct script access allowed');

  class Params {
    public function extractUserParam($text)
    {      
      return array(
        "type" => substr($text, 0,1),
        "id" => substr($text, 1),
      );
    }
  }