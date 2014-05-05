<?php

class Spectacle
{
    protected $db = null;
    
    public function __construct($DB)
    {
        $this->db = $DB;
    }
    
    public function addSpectacle($spectacle_name)
    {
        if (count($this->db->selectCol('SELECT id FROM spectacles WHERE spectacle_name = ?',$spectacle_name))==0) {
            $this->db->query('INSERT INTO spectacles(id,spectacle_name) VALUES(NULL,?)', $spectacle_name);    
        }
        else
        {
            echo 'that spectacle('.$spectacle_name.') already exist<br>';
        }
    }

    public function addRole($spectacle_name,$role_name)
    {
        $id_spectacle = $this->db->selectCell('SELECT id FROM spectacles WHERE spectacle_name = ?',$spectacle_name);
        //$spectacleArray = $this->db->selectCol('SELECT spectacle_name FROM spectacles');
        if (isset($id_spectacle)) {
            if (count($this->db->selectCol('SELECT id FROM roles WHERE role = ?',$role_name)) == 0) {
                $this->db->query("INSERT INTO roles(id,id_spectacle,role,id_session) VALUES(NULL,?,?,?)", $id_spectacle, $role_name, '');
            }
            else
            {
                echo 'that role ('.$role_name.') alredy exist in '.$spectacle_name.'<br>';
            }
        }
        else
        {
            echo $spectacle_name.' not found<br >';
        }
    }

    public function delRoleById($spectacle_id, $role_name)
    {

    }

    public function delRole($spectacle_name,$role_name)
    {
        $id_spectacle = $this->db->selectCell('SELECT id FROM spectacles WHERE spectacle_name = ? LIMIT 1', $spectacle_name);
        if (isset($id_spectacle)){
            $id_role = $this->db->selectCell(
                'SELECT id FROM roles WHERE id_spectacle = ? AND role = ?',
                $id_spectacle,
                $role_name
            );
            if ( isset($id_role) ) {
                $this->db->query('DELETE FROM roles WHERE id = ?d', $id_role);
            }
            else
            {
                //throw new Exception('tra-ta-ta');
                echo 'that role ('.$role_name.') does not exist<br>';
            }
        }
        else
        {
            echo 'that spectacle ('.$spectacle_name.') does not exist<br>';
        }
    }

    public function delSpectacle($spectacle_name)
    {
        $id_spectacle = $this->db->selectCell('SELECT id FROM spectacles WHERE spectacle_name = ? LIMIT 1',$spectacle_name);
        if (isset($id_spectacle)){
            $roles_array = $this->db->selectCol('SELECT role FROM roles WHERE id_spectacle = ?',$id_spectacle);
            //if (count($roles_array)>0){
                foreach($roles_array as $role){
                    $this->delRole($spectacle_name, $role);
                }
            //}
            $this->db->query('DELETE FROM spectacles WHERE id = ?',$id_spectacle);
        }
        else
        {
            echo 'that spectacle ('.$spectacle_name.') does not exist<br>';
        }
    }

    public function selectFreeRolesId($spectacle_name)
    {
        $id_spectacle = $this->db->selectCell('SELECT id FROM spectacles WHERE spectacle_name = ? LIMIT 1',$spectacle_name);
        if ( isset($id_spectacle) ) {
            $roleArray = $this->db->selectCol('SELECT id FROM roles WHERE id_spectacle = ? AND id_session = ""',$id_spectacle);
            if (count($roleArray) == 0){
                //all roles busy
                return 0;
            }
            else
            {
                return $roleArray;
            }
        }
        else
        {
            //spectacle not exist
            return -1;
        }
    }
    
    public function spectacleList()
    {
        $list = $this->db->selectCol('SELECT spectacle_name FROM spectacles');
        if (count($list) > 0){
            return $list;
        }
        else
        {
            //table of spectacles empty
            return 0;
        }
    }
    
    public function checkId($spectacle_name, $id_session)
    {
        $id_spectacle = $this->db->selectCell('SELECT id FROM spectacles WHERE spectacle_name = ?',$spectacle_name);
        $role = $this->db->selectCell('SELECT role FROM roles WHERE id_spectacle = ? AND id_session = ?',$id_spectacle,$id_session);

        return ( empty($role) ? 0 : $role );
    }
    
    public function setUserToRole($session, $role_id)
    {

        $this->db->query(
            'UPDATE roles SET id_session=? WHERE id = ?d',
            $session,
            $role_id
        );

    }


}
