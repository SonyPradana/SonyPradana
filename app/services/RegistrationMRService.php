<?php

use Convert\Converter\ConvertCode;
use Model\Simpus\MedicalRecord;
use Model\Simpus\RegistrationRecord;
use Model\Simpus\RegistrationRecords;
use Model\Simpus\Relation;
use Simpus\Apps\Service;
use System\Database\MyQuery;

class RegistrationMRService extends Service
{

  /**
   * contruct perent first
   */
  public function __construct()
  {
    parent::__construct();
    // put your code here
    $this->useAuth();
  }

  /**
   * Lihat kunjungan pasien, dengan parameter waktu.
   * Require:
   * - tanggal_kunjungan -> mm/dd/yyyy (string)
   *
   * @param array $request Http web request
   * @return array Data
   */
  public function LihatKunjungan(array $request): array
  {
    $day = $request['tanggal_kunjungan'] ?? "today";
    $current = strtotime($day);

    $data = RegistrationRecords::call()
      ->fillterTanggal($current)
      ->limitEnd(50)
      ->result();

    $data = array_map(function($item) {
      $item['tanggal_dibuat'] = date("H:i:s", $item['tanggal_dibuat']);
      $item['last_update'] = date("H:i:s", $item['last_update']);

      $status = $item['status'];
      if ($status == 0) {
        $item['status'] = 'pendaftaran';
      }

      // $jk = $item['jk'];
      // if ($jk == 0) {
      //   $item['jk'] = 'dipanggil';
      // }

      $umur = date_diff(date_create($item['umur']), date_create());
      if ($umur->days > 365) {
        $item['umur'] = round($umur->days / 365) . " thn";
      } else {
        $item['umur'] = round($umur->days / 30) . " bln";
        if ($umur->days < 31) {
          $item['umur'] = round($umur->days) . " hri";
        }
      }

      return $item;
    }, $data);

    return array(
      'status'  => 'oke',
      'code'    => 200,
      'data'    => $data,
      'error'   => false,
      'headers' => array('HTTP/1.1 200 Oke')
    );
  }

  /**
   * Menambahkan data kunjungan pendaftaran loket
   * Require:
   * - method put
   * - tanggal_kunjungan -> mm/dd/yyy (string)
   * - rm_id -> id nomor rekam medis (int)
   * - poli_tujuan -> poli tujuan (string)
   * - jenis_peserta -> code perserta (int)
   * - status_kunjungan -> status kunjungan (int)
   *
   * @param array $request Http web request
   * @return array Data
   */
  public function TambahKunjungan(array $request): array
  {
    // pre requests
    if ($request['x-method'] != 'put') {
      $this->error(400);
    }
    // set tanggal sesuai request
    if (isset($request['tanggal_kunjungan'])) {
      $time = date("H:i:s");
      $current = strtotime($request['tanggal_kunjungan'] . ' ' . $time);
    } else {
      $current = time();
    }

    // validasi id
    $id = $request['rm_id'] ?? -1;
    $medicalRecord = MedicalRecord::withId($id);
    if (!$medicalRecord->isIdExis()) {
      $this->errorHandler(400);
    }
    $crete_time = $medicalRecord->getDataDibuat();

    // cek kujungan hari ini
    $tanggal_kunjungan = strtotime($request['tanggal_kunjungan'] ?? 'today');
    $cek_kunjungan = RegistrationRecords::call()
      ->fillterNomorRm($medicalRecord->getNomorRM())
      ->fillterTanggal($tanggal_kunjungan)
      ->result();

    if ($cek_kunjungan || $tanggal_kunjungan > time()) {
      $this->errorHandler(400);
    }

    // cek id hash
    if (Relation::has_timestamp($crete_time)) {
      $id_hash = ConvertCode::ConvertToCode($crete_time);
    } else {
      $id_hash = ConvertCode::ConvertToCode($crete_time);
      Relation::creat($id_hash, $crete_time);
    }

    // model
    $kunjungan = new RegistrationRecord();
    $kunjungan
      ->setId_hash($id_hash)
      ->setTanggal_dibuat($current)
      ->setLast_update($current)
      ->setNomor_rm($medicalRecord->getNomorRM())
      ->setPoli($request['poli_tujuan'] ?? 'umum')
      ->setStatus(0)
      ->setJenis_peserta($request['jenis_peserta'] ?? 0)
      ->setPoli_id('')
      ->setStatus_kunjungan($request['status_kunjungan'] ?? 0)
      ;

    $status = $kunjungan->cread();

    // hide some information to client
    $data = $kunjungan->convertToArray();
    $data['nama'] = $medicalRecord->getNama() ?? '';
    $umur = date_diff(date_create($medicalRecord->getTangalLahir()), date_create());
    if ($umur->days > 365) {
      $data['umur'] = round($umur->days / 365) . " thn";
    } else {
      $data['umur'] = round($umur->days / 30) . " bln";
      if ($umur->days < 31) {
        $data['umur'] = round($umur->days) . " hri";
      }
    }
    $data['tanggal_dibuat'] = date('H:i:s');
    unset($data['id_hash']);

    return array(
      'status'  => $status ? 'ok' : 'bad request',
      'code'    => 200,
      'data'    => $data ?? [],
      'error'   => false,
      'headers' => array('HTTP/1.1 200 Oke')
    );
  }

  /**
   * Menhapus kunjungan pendaftaran sesuai dengan id-nya
   * Require:
   * - method delete
   * - kunjungan_id -> nomor id kunjungan (int)
   * - status_kunjungan -> status kunjungan (int)
   *
   * @param array $request Http web request
   * @return array Data
   */
  public function HapusKunjungan(array $request): array
  {
    if ($request['x-method'] != 'delete') {
      $this->error(400);
    }

    $id = $request['kunjungan_id'] ?? -1;
    $data = MyQuery::conn("data_kunjungan")
      ->select()
      ->equal('id', $id)
      ->single();

    $status = MyQuery::conn("data_kunjungan")
      ->delete()
      ->equal("id", $id)
      ->execute();

    return array(
      'status'  => $status ? 'accepted' : 'error',
      'code'    => 202,
      'data'    => $data,
      'error'   => false,
      'headers' => array('HTTP/1.1 202 Accepted')
    );
  }

  /**
   * mengedit data kunjungan pendaftaran sesuai dengan id-nya
   * Require:
   * - kunjungan_id -> nomor id kunjungan (int)
   * - poli_tujuan -> poli tujuan (string)
   * - status -> status tindakan (int)
   * - jenis_peserta -> code perserta (int)
   * - poli-id -> id table data_poli (int)
   *
   * @param array $request Http web request
   * @return array Data
   */
  public function EditKunjungan(array $request): array
  {
    $id = $request['kunjungan_id'] ?? -1;
    $kunjungan = new RegistrationRecord();
    $kunjungan->setID($id);

    if ($status = $kunjungan->read()) {

      $data_before = $kunjungan->convertToArray();

      $kunjungan
        ->setPoli($request['poli_tujuan'] ?? null)
        ->setStatus($request['status'] ?? null)
        ->setJenis_peserta($request['jenis_peserta'] ?? null)
        ->setPoli_id($request['poli-id'] ?? null)
        ->setStatus_kunjungan($request['status_kunjungan'] ?? null);

      $data_after = $kunjungan->convertToArray();

      if ($data_after != $data_before) {
        // update kunjungan
        $status = $kunjungan
          ->setLast_update(time())
          ->update();
      } else {
        $error = ['data'  => 'nothink to change!'];
      }
    }
    $data = $kunjungan->convertToArray();

    return array(
      'status'  => $status ? 'ok' : 'bad request',
      'code'    => 200,
      'data'    => $data ?? [],
      'error'   => $error ?? false,
      'headers' => array('HTTP/1.1 200 Oke')
    );
  }
}

