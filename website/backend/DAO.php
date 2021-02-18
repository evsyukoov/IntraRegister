<?php
require_once("MailSender.php");

class DAO
{
    private const ADDRESS = "127.0.0.1";
    private const USER = "root";
    private const PASS = "1111";
    private const BD_NAME = "intra";

    private const INSERT = "INSERT INTO users VALUES(?,?,?,?)";

    //private const SELECT_HASH = "SELECT login FROM users WHERE hash=?";

    private $login;

    private $select;

    private $insert;

    private $link;
    /**
     * DAO constructor.
     * @param $login
     */
    public function __construct($login)
    {
        if ($login == null)
            return ;
        $this->login = $login;
        $this->select = sprintf("SELECT * FROM users WHERE login='%s'", $login);
    }

    private function updateTable(IntraAPI $api, $hash)
    {
        $stmt = mysqli_prepare($this->link, self::INSERT);
        if (!$stmt) {
            echo "Проблема 1";
            return;
        }
        $email = $api->getEmail();
        $status = 0;
        if (!$stmt->bind_param('ssdd', $this->login, $email, $status, $hash)) {
            echo "Проблема 2";
            return ;
        }
        $stmt->execute();
        $stmt->close();
    }

    public function findUserFromHash($hash)
    {
        if ($this->initConnection() == 0) {
            echo 'Приложение поломалось';
            return false;
        }
        $result = mysqli_query($this->link, sprintf("SELECT login, status FROM users WHERE hash='%d'",$hash));
        if ($result->num_rows == 0) {
            echo 'Похоже что это не ваша ссылка';
            mysqli_close($this->link);
            return (false);
        }
        $row = $result->fetch_row();
        //уже подтвержденный email
        if ($row[1] == 1)
        {
            echo 'Email уже подтвержден';
            mysqli_close($this->link);
            return false;
        }
        return ($row[0]);
    }

    public  function updateUserStatus($user)
    {
        $result = mysqli_query($this->link, sprintf("UPDATE users SET status=1 WHERE login='%s'", $user));
        if (!$result)
            echo 'Что-то пошло не так';
        else
            echo 'Теперь ' . $user . " ты будешь получать информацию об экзаменах" . "\n";
        mysqli_close($this->link);

    }

    private function findUserStatus(mysqli_result $result)
    {
        $row = $result->fetch_row();
        return $row[2];
    }

    public function  addUser()
    {
        if (!$this->initConnection())
            echo "Приложение поломалось";
        $result = mysqli_query($this->link, $this->select);
        if (!$result)
            echo "Приложение поломалось";
        else if ($result->num_rows == 0) {
            $api = new IntraAPI($this->login);
            if (($ret = $api->makeRequestToIntra()) == 0)
                echo "Проблемы на сервере Intra";
            else if ($ret == -1)
                echo "Нет такого логина в системе Intra";
            else {
                $hash = md5($this->login . time());
                $this->updateTable($api, $hash);
                $sender = new MailSender($api->getEmail(), $hash);
                $sender->sendEmail();
            }
        }
        else{
            // пользователь есть в БД, но не подтвердил почту
            $status = $this->findUserStatus($result);
            if ($status == 0)
                echo 'Чтобы получать информацию об экзаменах нужно подтвердить почту';
            else
                echo "Вы уже подписались на уведомления об экзаменах";
        }
        mysqli_close($this->link);
    }

    public function initConnection()
    {
        $this->link = mysqli_connect(self::ADDRESS, self::USER, self::PASS, self::BD_NAME);
        if ($this->link == false)
            return false;
        else
            return true;
    }
}