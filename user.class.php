<?php 

/*
Copyright 2016 Koen Hollander
*/

Class user {
	
	protected $con = "";
	
	public function __construct($con) {
		@$this->con = $con;
	}
	
	public function add ($username,$password,$email) {
		if (!isset($username) || trim($username) == NULL) {
			return false;
			exit;
		}
		if (!isset($password) || trim($password) == NULL) {
			return false;
			exit;
		}
		if (!isset($email) || trim($email) == NULL) {
			return false;
			exit;
		}
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			return false;
			exit;
		}
		$q = $this->con->query 
		("
			INSERT INTO users 
			(
			id,
			username,
			password,
			email
			)
			VALUES
			(
			'',
			'".$this->con->real_escape_string($username)."',
			'".$this->con->real_escape_string(password_hash($password, PASSWORD_BCRYPT))."',
			'".$this->con->real_escape_string($email)."'
			)
		");
		if ($q) {
			unset($username);
			unset($password);
			unset($email);
			return true;
			exit;
		} else {
			return false;
			exit;
		}
	}
	public function login ($username,$password) {
		if (!isset($username) || trim($username) == NULL) {
			return false;
			exit;
		}
		if (!isset($password) || trim($password) == NULL) {
			return false;
			exit;
		}
		$q = $this->con->query
		("
		SELECT 
		password,
		id 
		FROM 
		users
		WHERE
		username = '".$this->con->real_escape_string($username)."'
		LIMIT 1
		");
		if ($q) {
			$r = $q->fetch_assoc();
			if (password_verify($password,$r['password'])) {
				$session = new session($this->con);
				if ($session->create($r['id'])) {
					return true;
					exit;
				} elseif (!$session->create($r['id'])) {
					return false;
					exit;
				}
			} else {
				return false;
				exit;
			}
		} else {
			return false;
			exit;
		}
	}
}

Class session extends user {
	public function create ($uid){
		$sid = htmlspecialchars(sha1($uid.date('l jS \of F Y h:i:s A')));
		$q = $this->con->query 
		("
			INSERT INTO sessions
			(
			id,
			userid,
			sessionkey,
			status,
			ip
			)
			VALUES
			(
			'',
			'".$this->con->real_escape_string($uid)."',
			'".$this->con->real_escape_string($sid)."',
			'valid',
			'".$this->con->real_escape_string($_SERVER['REMOTE_ADDR'])."'
			)
		");
		if ($q) {
			if ($this->cookies($uid,$sid)) {
				return true;
				exit;
			}
		} else {
			return false;
			exit;
		}
	}
	
	protected function cookies ($uid,$sid) {
		$_SESSION['uid'] = htmlspecialchars($uid);
		$_SESSION['sid'] = htmlspecialchars($sid);
		return true;
	}
	
	public function check($sid,$uid) {
		if (!isset($sid) || trim($sid) == NULL) {
			return false;
			exit;
		}
		
		if (!isset($uid) || trim((int)$uid) == NULL) {
			return false;
			exit;
		}
		
		$q = $this->con->query
		("
		SELECT userid
		FROM 
		sessions
		WHERE
		sessionkey = '".$this->con->real_escape_string($sid)."'
		AND 
		status = 'valid'
		AND
		ip = '".$this->con->real_escape_string($_SERVER['REMOTE_ADDR'])."'
		LIMIT 1
		");
		
		if ($q) {
			$r = $q->fetch_assoc();
			if ($uid == $r['userid']) {
				return true;
				exit;
			} elseif ($uid != $r['userid']) {
				return false;
				exit;
			}
		} else {
			return false;
			exit;
		}
	}
	public function destroy ($sid) {
		if (!isset($sid) || trim($sid) == NULL) {
			return false;
			exit;
		}
		
		$q = $this->con->query
		("
		UPDATE 
		sessions
		SET 
		status = 'invalid'
		WHERE 
		sessionkey = '".$this->con->real_escape_string($sid)."'
		");
		
		if ($q) {
			if (session_destroy()) {
				unset($_SESSION);
				return true;
				exit;
			} elseif (!session_destroy()) {
				return false;
				exit;
			}
		} else {
			return false;
			exit;
		}
	}
}
?>
