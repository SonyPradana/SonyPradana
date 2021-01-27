<?php

use Model\UserRegister\userRegister;
use Model\UserRegister\userRegisters;
use Simpus\Apps\Middleware;
use Simpus\Helper\HttpHeader;
use Simpus\Auth\Auth;
use System\Database\MyModel;
use System\Database\MyPDO;
use System\Database\MyQuery;

class UserRegisterService extends Middleware
{
  // private function
  private function useAuth()
  {
    // cek access
    $token = Middleware::getMiddleware()['auth']['token'];
    $auth = new Auth($token, Auth::USER_NAME_AND_USER_AGENT_IP);

    if (! $auth->privilege('admin')) {
      HttpHeader::printJson(['status' => 'unauthorized'], 500, [
        "headers" => array (
          'HTTP/1.0 401 Unauthorized',
          'Content-Type: application/json'
        )
      ]);
    }
  }

  private function errorhandler(array $error_provider)
  {
    $printed_data = array(
      'status' => 'bad request',
      'code'   => 400,
      'error'  => $error_provider,
    );

    HttpHeader::printJson($printed_data, 500, [
      "headers" => array (
        'HTTP/1.1 400 Bad Request',
        'Content-Type: application/json'
      )
    ]);
  }

  protected $PDO = null;
  public function __construct(MyPDO $PDO = null)
  {
    $this->PDO =$PDO ?? new MyPDO();
    $this->useAuth();
  }

  public function request(array $request): array
  {
    $data = new userRegisters($this->PDO);
    $data->order('id', MyModel::ORDER_DESC);

    return array (
      'status'  => 'ok',
      'data'    => $data->resultAll(),
      'error'   => false,
      'headers' => array('HTTP/1.1 200 Oke')
    );
  }

  public function acceptUser(array $request)
  {
    $id = $request['user_id'] ?? $this->errorhandler([]);

    $user = new userRegister($this->PDO);
    $user->setID($id);

    $status = 'failed';

    if ($user->isExist() && $user->read()) {
      // dbs
      $data = $user->convertToArray();
      $db   = new MyQuery($this->PDO);

      // error provider
      $error = $this->validateInput($data);

      if (empty($error)) {
        $this->PDO->beginTransaction();
        // pindah ke table user
        $db
          ->insert('users')
          ->value('user', $data['user'])
          ->value('pwd', $data['pwd'])
          ->value('stat', '25')
          ->value('bane', time())
          ->execute();
        $tanferToUserTables = $this->PDO->rowCount();

        // pindah ke table profile
        $db->reset();
        $db
          ->insert('profiles')
          ->value('user', $data['user'])
          ->value('email', $data['email'])
          ->value('display_name', $data['disp_name'])
          ->value('section', 'Rekam Medis')
          ->value('display_picture', '/public/data/img/display-picture/user/no-image.png')
          ->execute();
        $tanferToUserProfies = $this->PDO->rowCount();

        // hapus request regestrasi
        $deleteRegister = $user->delete();

        if ($tanferToUserProfies > 0
        && $tanferToUserProfies > 0
        && $deleteRegister) {
          // simpan data jika
          // user tersimpan, prodile terismpan, registartion dihapus

          $status = 'oke';
          $this->PDO->endTransaction();
        } else {
          // cansel saat satu prosses gagal
          $error = array(
            'server' => 'failed to saving data'
          );
          $this->PDO->cancelTransaction();
        }
      }

      return array (
        'status' => $status,
        'code'  => 200,
        'data' => array(),
        'error' =>$error,
        'headers' => array('HTTP/1.1 200 Oke')
      );

    }

    $this->errorhandler([]);
  }

  public function declineUser(array $request)
  {
    $user_id = $request['user_id'] ?? $this->errorhandler(['error' => 'invalid params']);
    // cek user
    $user = new userRegister($this->PDO);
    $user->setID($user_id);

    // error provder
    [$error, $data] = [array(), array()];

    if ($user->isExist())
    {
      $user->delete();
      $data = array(
        'user' => 'user has been deleted'
      );
      $error = array();
    }
    return array(
      'status' => empty($error) ? 'ok' : 'invalid',
      'code'  => 200,
      'data' => $data,
      'error' => array(
        'user' => 'user not exist'
      ),
      'headers' => array('HTTP/1.1 200 Oke')
    );
  }

  private function validateInput(array $request): array
  {
    $validation = new GUMP('id');
    $validation->validation_rules(
      array (
        'user'  => 'required|alpha|min_len,4|max_len,10',
        'email' => 'required|valid_email',
        'disp_name' => 'required|valid_name'
      )
    );
    $validation->run($request);

    return $validation->get_errors_array();
  }
}

