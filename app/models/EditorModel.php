<?php

class EditorModel extends Model {

    public function __construct() {

        parent::__construct();

    }

    //TODO: CODE DUPLICATION WITH FILESTORAGEMODEL NSERT_FILE METHOD
    public function save_file($filename,$content) {
        if (preg_match('/[%?^#&!~ˇ˘°˛˙´˙`˛°˘]/',$filename)) {
            return 1;
        }
        $path="../app/files/temp/file";
        file_put_contents($path,$content);
        $size=filesize($path);
        $type="text/plain";
        $userid=$this->getId();
        try {
            $stmt = $this->db->prepare('INSERT INTO files(file_name,file_size,file_type,user_id,sender_id)
                                                                    VALUES(:filename,:filesize,:filetype,:userid,:senderid)');
            $stmt->execute(["filename" => $filename,"filesize"=>$size,"filetype"=>$type ,"userid"=>$userid,"senderid"=>null]);
        } catch (PDOException $ex) {
            echo $ex;
            return 2;
        }
        $id=$this->db->lastInsertId();
        $username=Session::get("user_name");
        rename($path,"../app/files/".$username."/".$id.".txt");
        return 0;



    }


    private function getId() {
        $user=Session::get("user_name");
        $stmt = $this->db->prepare("SELECT user_id FROM users WHERE user_uname=:uname");
        $stmt->execute(["uname" =>$user ]);
        //TODO: JAVÍTANI AZ ÖSSZES HELYEN A DUPLIKÁCIÓT
        $userid = $stmt->fetch(PDO::FETCH_ASSOC);
        return $userid["user_id"];

    }

}