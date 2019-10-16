<?php
  // Read .env
  try {
    $dotenv = Dotenv\Dotenv::create(__DIR__);
    $dotenv->load();
  } catch(InvalidArgumentException $ex) {
    // Ignore if no dotenv
  }
