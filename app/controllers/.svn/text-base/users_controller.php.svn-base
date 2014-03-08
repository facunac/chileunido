<?php

class UsersController extends AppController {
	var $title = 'Users';

	/**
	 * Override beforeFilter in AppController
	 */
	function beforeFilter() {
		parent::beforeFilter();
		$this->User = new User();
	}

	/**
	 * Register a new user
	 */
    function register() {

        if (!empty($this->data)) {
            // Create user
            $this->data['password'] = md5($this->data['password']);
            if ($this->User->save($this->data)) {

                // Login User
                $user = $this->User->read();
                $this->Session->write('User', $user['User']);
                $this->redirect('/');
            }
        }
    }

	/**
	 * Login existing user
	 */
    function login() {

        if (empty($this->data)) {
            //Don't show the error message if no data has been submitted.
            $this->set('error', false);
        } else {
            // Start authentication...
            $authenticated = false;

            $someone = $this->User->findByUsername($this->data['User']['username']);

            // At this point, $someone is full of user data, or its empty.
            if (!empty($someone['User']['password'])) {
		if ($someone['User']['password'] == md5($this->params['data']['User']['password'])) {
		$authenticated = true;
		}
            }

            if ($authenticated) {
                $this->Session->write('User', $someone['User']);
				// Including the PHPBB_Login File
				require_once('PHPBB_Login.php');
				// Then login the user to the forum
				$phpBB = new PHPBB_Login();
				$phpBB->login($someone['User']['id'], $someone['User']['username'], $someone['User']['password']);

                // Now that we have them stored in a session, forward them on
                // to a landing page for the application.
                $this->redirect('/forums'); // TODO: find where we came from and redirect to that page
            } else {
                // set the $error var in the view to true:
                $this->set('error', true);
            }
        }
    }


	/**
	 * Logout user
	 */
    function logout() {
	$user_data = $this->Session->read('User');
        $user_id = $user_data['id'];

	require_once('PHPBB_Login.php');
	// Log the user out the forum
	$phpBB = new PHPBB_Login();
	$session_id = session_id();
	if(!empty($session_id) && !empty($user_id))
		$phpBB->logout($session_id, $user_id);
	$this->Session->delete('User');

	$this->redirect('/');
    }

}

?>
