<?php

namespace ttm4135\webapp\models;

class User
{
    const INSERT_QUERY = "INSERT INTO users(username, password, email, bio, isadmin) VALUES(?, ?, ? , ? , ?)";
    const UPDATE_QUERY = "UPDATE users SET username=?, password=?, email=?, bio=?, isadmin=? WHERE id=?";
    const DELETE_QUERY = "DELETE FROM users WHERE id=?";
    const FIND_BY_NAME_QUERY = "SELECT * FROM users WHERE username=?";
    const FIND_BY_ID_QUERY = "SELECT * FROM users WHERE id=?";
    const INSERT_AUTH_KEY = "UPDATE users SET auth_key=? WHERE id=?";
    const UNINSERT_AUTH_KEY = "UPDATE users SET auth_key=NULL WHERE id=?";
    const GET_OFFICIAL_AUTH_KEY = "SELECT auth_key FROM users WHERE id=?";
    const FIND_AUTH = "SELECT auth_key FROM users WHERE id=?";


    protected $id = null;
    protected $username;
    protected $password;
    protected $email;
    protected $bio = 'Bio is empty.';
    protected $isAdmin = 0;

    static $app;


    static function make($id, $username, $password, $email, $bio, $isAdmin )
    {
        $user = new User();
        $user->id = $id;
        $user->username = $username;
        $user->password = $password;
        $user->email = $email;
        $user->bio = $bio;
        $user->isAdmin = $isAdmin;

        return $user;
    }

    static function makeEmpty()
    {
        return new User();
    }

    /**
     * Insert or update a user object to db.
     */
    // function save()
    // {
    //     if ($this->id === null) {
    //         $query = sprintf(self::INSERT_QUERY,
    //             $this->username,
    //             $this->password,
    //             $this->email,
    //             $this->bio,
    //             $this->isAdmin            );
    //     } else {
    //       $query = sprintf(self::UPDATE_QUERY,
    //             $this->username,
    //             $this->password,
    //             $this->email,
    //             $this->bio,
    //             $this->isAdmin,
    //             $this->id
    //         );
    //     }
    //   return self::$app->db->exec($query);
    // }

    function save() {
        if ($this->id === null) {
            return self::create();
        } else {
            return self::update();
        }
    }

    function create()
    {
      $stmt = self::$app->db->prepare(self::INSERT_QUERY);
      $stmt->bindParam(1, $this->username);
      $stmt->bindParam(2, $this->password);
      $stmt->bindParam(3, $this->email);
      $stmt->bindParam(4, $this->bio);
      $stmt->bindParam(5, $this->isAdmin);
    	return $stmt->execute();
    }

    function update()
    {
      $stmt = self::$app->db->prepare(self::UPDATE_QUERY);
      $stmt->bindParam(1, $this->username);
      $stmt->bindParam(2, $this->password);
      $stmt->bindParam(3, $this->email);
      $stmt->bindParam(4, $this->bio);
      $stmt->bindParam(5, $this->isAdmin);
      return $stmt->execute();
    }

    // function delete()
    // {
    //     $query = sprintf(self::DELETE_QUERY,
    //         $this->id
    //     );
    //     return self::$app->db->exec($query);
    // }
    function delete() {
      $stmt = self::$app->db->prepare(self::DELETE_QUERY);
      $stmt->bindParam(1, $this->id);
      return $stmt->execute();
    }

    function getId()
    {
        return $this->id;
    }

    function getUsername()
    {
        return $this->username;
    }

    function getPassword()
    {
        return $this->password;
    }

    function getEmail()
    {
        return $this->email;
    }

    function getBio()
    {
        return $this->bio;
    }

    function isAdmin()
    {
        return $this->isAdmin === "1";
    }

    function setId($id)
    {
        $this->id = $id;
    }

    function setUsername($username)
    {
        $this->username = $username;
    }

    function setPassword($password)
    {
        $this->password = $password;
    }

    function setEmail($email)
    {
        $this->email = $email;
    }

    function setBio($bio)
    {
        $this->bio = $bio;
    }
    function setIsAdmin($isAdmin)
    {
        $this->isAdmin = $isAdmin;
    }

    static function insertAuthKey($key, $uid){
      $stmt = self::$app->db->prepare(self::INSERT_AUTH_KEY);
      $stmt->bindParam(1, $key);
      $stmt->bindParam(2, $uid);
      $stmt->execute();
    }

    static function unInsertAuthKey($uid) {
        $stmt = self::$app->db->prepare(self::UNINSERT_AUTH_KEY);
        $stmt->bindParam(1, $uid);
        $stmt->execute();
    }
    function getOfficialAuthKey($uid) {
        $stmt = self::$app->db->prepare(self::GET_OFFICIAL_AUTH_KEY);
        $stmt->bindParam(1, $uid);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['auth_key'];
    }
    static function hasAuthKey() {
	     $auth_key = self::getOfficialAuthKey();
	     return $auth_key != null;
    }

    static function findAuthKey($uid){
      $stmt = self::$app->db->prepare(self::FIND_AUTH);
      $stmt->bindParam(1, $uid);
      $stmt->execute();
    }

    /**
     * Get user in db by userid
     *
     * @param string $userid
     * @return mixed User or null if not found.
     */
    static function findById($userid)
    {
      $stmt = self::$app->db->prepare(self::FIND_BY_ID_QUERY);
      $stmt->bindParam(1, $userid);
      $stmt->execute();
      $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if($row == false) {
            return null;
        }

        return User::makeFromSql($row);
    }

    /**
     * Find user in db by username.
     *
     * @param string $username
     * @return mixed User or null if not found.
     */
    static function findByUser($username)
    {
      $stmt = self::$app->db->prepare(self::FIND_BY_NAME_QUERY);
      $stmt->bindParam(1, $username);
      $stmt->execute();
      $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if($row == false) {
            return null;
        }

        return User::makeFromSql($row);
    }


    static function makeFromSql($row)
    {
        return User::make(
            $row['id'],
            $row['username'],
            $row['password'],
            $row['email'],
            $row['bio'],
            $row['isadmin'],
            $row['auth_key']
        );
    }

}


  User::$app = \Slim\Slim::getInstance();
