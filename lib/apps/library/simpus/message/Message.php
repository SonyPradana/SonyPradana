<?php namespace Simpus\Message;

use System\Database\MyPDO;

/**
 * perent class untuk pengiriman pesan
 * 
 * @author sonypradana@gmail.com
 */
abstract class Message
{
    /** @var string pengirim pesan */
    protected $_sender;
    /** @var string penerima pesan */
    protected $_resiver;
    /** @var string type / category / jenis dari pesan yang dikirm*/
    protected $_type;
    /** @var string isi pesan pesan (maksimal 250 karakter) */
    protected $_message;
    /** @var string tanggal pesan dikirim */
    protected $_date;
    /** @var string inforamsi berupa meata berformat json */
    protected $_meta; // json format

    public function __construct()
    {
        
    }

    /** kirim pesan dengan foramt yang telah di buat saat conntruction 
     * 
     * @return boolean
     * -true jika pesan berhaisl disimpan
     * -false jika pesan gagal disimpan
    */
    public function kirimPesan(MyPDO $PDO = null)
    {
        // koneksi data base
        $db = $PDO ?? new MyPDO();

        $sender = $this->_sender; $resiver = $this->_resiver;
        $type = $this->_type; $date = $this->_date;
        $msg = $this->_message; $meta = $this->_meta;
        // query      
        $db->query(
            "INSERT INTO `public_message`
                (`id`, `sender`, `resiver`, `type`, `date`, `message`, `meta`)
            VALUES
                (:id, :sender, :resiver, :type, :date, :msg, :meta)
        ");
        $db->bind(':id', '');
        $db->bind(':sender', $sender);
        $db->bind(':resiver', $resiver);
        $db->bind(':type', $type);
        $db->bind(':date', $date);
        $db->bind(':msg', $msg);
        $db->bind(':meta', $meta);
        // esekusi query
        $db->execute();
        // bila berhasil return true
        if ($db->rowCount() > 0) {
            return true;
        }
        // defult nya adalah salah
        return false; // true jika pesan berhasil dikirim
    }

    /** mendeteksi pesan ini termasuk spam atau tidak 
     * 
     * @return boolean
     * -true artinya pesan termasuk spam
     * -false artinya pesan tidak termasuk spam
    */
    public function spamDetector()
    {
        // detact by ip / user name
        // scope pertama (sender):
        // step 1:  ambil 5 data terahir dari sender yang sama di database
        // step 2:  cek sender dan resiver yang sama dari request
        // step 3:  jika sama -> cek selisihnya -> selisih waktu < 12 jam => spamer
        // scope kedua (resiver):
        // berlaku hal yang sama       
        $get_msg = new ReadMessage();
        $msg = $get_msg
            ->filterByPengirim($this->_sender)
            ->filterByPenerima($this->_resiver)
            ->filterByDate(time() - 120, '>')
            ->limitView(5)
            ->bacaPesan();
        if (count($msg) > 0) {
            return true;
        }

        // detact by isi pesan -> tergantung type pesan
        // metode 1:
        // jika type != kepuasan
        // step 1:  lihat pesan terahir dengan sender yang sama
        // step 2:  lihat kesmaan -> kesamaan > 80% => spamer
        // begitu juga untuk resiver
        // metode 2:
        // menggunakn content weight, dan compare dari databse spamer
        
        return false; // true jika pesan berupa spam
    }
}
