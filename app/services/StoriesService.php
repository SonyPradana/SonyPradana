<?php

use Convert\Converter\ConvertCode;
use Gumlet\ImageResize;
use Model\Stories\Stories;
use Model\Stories\Story;
use Simpus\Apps\Service;
use System\Database\MyPDO;
use System\File\UploadFile;

class StoriesService extends Service
{
  protected $PDO = null;

  public function __construct(MyPDO $PDO = null)
  {
    $this->error = new DefaultService();
    $this->PDO = $PDO ?? MyPDO::getInstance();
  }

  public function Upload(array $request): array
  {
    // validation
    $validation = new GUMP('id');
    $validation->validation_rules(array (
      'caption'  => 'alpha_numeric_space|max_len,30',
      'uploader' => 'alpha_numeric_space|max_len,15',
      'end'      => 'required'
    ));
    $validation->run($request);

    // property
    $error = $validation->get_errors_array();
    $status = 'bad request';
    $storyID = ConvertCode::ConvertToCode(time());

    // upload and save database
    if (! $validation->errors()
    && isset($request['files']['upload_stories'])) {

      $extension = explode('.', $request['files']['upload_stories']['name']);
      $fileExtension = strtolower( end( $extension ) );
      // uplad image
      $upload = new UploadFile($request['files']['upload_stories']);
      $imgURL = $upload->setFileName($storyID)
        ->setFolderLocation('/public/data/img/stories/original/')
        ->setMimeTypes(array('image/jpg', 'image/jpeg', 'image/png'))
        ->setMaxFileSize( 5_242_880 )
        ->upload();

      // save to db if upload success
      if ($upload->Success()) {
        $stories = new Story($this->PDO);
        $stories
          ->setDateTaken(time())
          ->setDateEnd(time() + $request['end'])
          ->setImageID("$storyID.$fileExtension")
          ->setCaption($request['caption'])
          ->setUploader($request['uploader'] ?? $_SERVER['REMOTE_ADDR'] ?? '')
          ->setViewer(0)
          ->cread();
        if ($stories) {
          // resize image
          (new ImageResize(BASEURL . $imgURL))->resizeToWidth(749)
            ->save(BASEURL . $imgURL);
          (new ImageResize(BASEURL . $imgURL))->resizeToWidth(200)
            ->save(BASEURL . "/public/data/img/stories/thumbnail/$storyID.$fileExtension");
          (new ImageResize(BASEURL . $imgURL))->resizeToShortSide(50)
            ->save(BASEURL . "/public/data/img/stories/small/$storyID.$fileExtension");

          // berhasil dikirim
          $status = 'ok';
          $error = false;
        } else {
          $error['server'] = "can't upload to server";
        }

      }
    } else {
      $error['file'] = 'File upload tidak ditemukan';
    }

    return array(
      'status'  => $status,
      'code'    => 200,
      'params'  => $request,
      'data'    => null,
      'error'   => $error,
      'headers' => array('HTTP/1.1 200 Oke')
    );
  }

  public function Add_Viewer(array $params): array
  {
    $storiesId = $params['stories_id'] ?? 0;

    // set up
    $stories = new Story($this->PDO);
    $stories->setID($storiesId);

    if ($stories->isExist()) {
      $stories->read();
      $lastViewer = $stories->getViewer();
      $stories->setViewer($lastViewer + 1);
      $stories->update();

      return array (
        'status'  => 'ok',
        'code'    => 200,
        'data'    => array (
          'viewer' => $lastViewer + 1
        ),
        'error'   => false,
        'headers' => array('HTTP/1.1 200 Oke')
      );
    }

    return array(
      'status'  => 'bad request',
      'data'    => null,
      'error'   => array (
        'story_id' => 'id tidak valid'),
      'headers' => array('HTTP/1.1 400 Bad Request')
    );

  }

  public function Deleted_Stories(array $request): array
  {
    $this->useAuth();

    $storyID = $request['stories_id'] ?? 0;
    if ($storyID == 0)
    {
      return $this->error(400);
    }

    // logic delete from database
    $story =  new Story($this->PDO);
    $story->setID($storyID)->read();
    $deleted = $story->delete();

    if ($deleted) {
      // deleted image
      unlink(BASEURL . '/data/img/stories/thumbnail/' . $story->getImageID());
      unlink(BASEURL . '/data/img/stories/small/' . $story->getImageID());
      unlink(BASEURL . '/data/img/stories/original/' . $story->getImageID());
    }

    return array (
      'status' => $deleted ? 'ok' : 'bad request',
      'code'  => 200,
    );
  }

  public function Stories(array $request): array
  {
    $stories = new Stories($this->PDO);

    return array (
      'status'  => 'ok',
      'code'    => 200,
      'data'    => $stories->resultAll(),
      'headers' => array('HTTP/1.1 200 Oke')
    );
  }

  public function Rolling(Array $request): array
  {
    $stories = new Stories($this->PDO);
    $groupName = $request['group_name'] ?? 'angger';
    $stories->filterByUploader($groupName);

    return array (
      'status'  => 'ok',
      'code'    => 200,
      'data'    => $stories->result(),
      'headers' => array('HTTP/1.1 200 Oke')
    );
  }

  public function Group_Story(array $request): array
  {
    $stories = new Stories($this->PDO);

    $data = null;
    foreach ($stories->getGroub() as $groupStory) {
      $uploader = $groupStory['uploader'];
      $uploader = $uploader == '' ? 'angger' : $uploader;

      $stories->filterByUploader($uploader);
      $this->PDO->query($stories->getQuery());
      $this->PDO->bind(':uploader', $uploader);

      $data[$uploader] = $this->PDO->single();

      // add slug story roll
      $slug = str_replace(' ', '+', $uploader);
      $data[$uploader]['slug'] = "/stories/roll/$slug";
    }

    return array (
      'status'  => 'ok',
      'code'    => 200,
      'data'    => $data,
      'headers' => array('HTTP/1.1 200 Oke')
    );
  }

}

