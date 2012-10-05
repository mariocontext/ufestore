<?php

class RegistrationController extends Controller {
  public function signup() {
    // sign up new user and send an email with welcome email
    $this->redirect("/");
  }
}

?>