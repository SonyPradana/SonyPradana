<?php namespace Model\Simpus;

use System\Database\MyPDO;
use \PDO;

class KIAAnakRecord
{
    /** @var MyPDO Instant PDO */
    private $PDO;
    
    private $_id_hash = null;
    // tambah id ibu
    private $_jenis_kelamin = 0;
    // biodata anak - kia-anak
    private $_bbl = 0;
    private $_pbl = 0;
    private $_kia = "";
    private $_imd = 0;
    private $_asi_eks = 0;
    private $_grups_posyandu = 0;
    // data pemeriksaan
    private $_data_rm ; // data rm/staging rm
    private $_data_posyandu = []; // array data posyandu

    // property - getter
    public function getJenisKelamin()
    {
        return $this->_jenis_kelamin;
    }
    public function getBeratBayiLahir()
    {
        return $this->_bbl;
    }
    public function getPanjangBayiLahir()
    {
        return $this->_pbl;
    }
    public function getKIA()
    {
        return $this->_kia;
    }
    public function getIndeksMasaTubuh()
    {
        return $this->_imd;
    }
    public function getAsiEks()
    {
        return $this->_asi_eks;
    }
    public function getGroubsPosyandu()
    {
        return $this->_grups_posyandu;
    }
    public function getDataPosyandu()
    {
        return $this->_data_posyandu;
    }
    // property - setter
    public function setIdHash(string $value)
    {
        $this->_id_hash = $value;
        return $this;
    }
    public function setBeratBayiLahir(int $value)
    {
        $this->_bbl = $value;
        return $this;
    }
    public function setPanjangBayiLahir(int $value)
    {
        $this->_pbl = $value;
        return $this;
    }
    public function setKIA($value)
    {
        $this->_kia = $value;
        return $this;
    }
    public function setIndeksMasaTubuh($value)
    {
        $this->_imd = $value;
        return $this;
    }
    public function setASIEksklusif(bool $value)
    {
        $this->_asi_eks = $value;
        return $this;
    }
    public function setGrubPosyandu(int $value)
    {
        $this->_grups_posyandu = $value;
        return $this;
    }
    public function tambahDataPosyanduBaru(PosyanduRecord $value){
        // membuat data baru di data base dan juga di kelas ini
        $this->_data_posyandu[] = $value;
        $value->creat( $this->_id_hash );
    }

    // constructor
    public function __construct()
    {
        $this->PDO = new MyPDO();
    }
    public function loadWithID($id_hash, bool $refresh = true)
    {
        $this->_id_hash = $id_hash;
        if ($refresh) {
            $this->refresh();
        }
    }
    // function
    /** 
     * Memuat ulang data berdasarkan id yang telah dibuat sebelumnya
     * @return bool True ketika data berhasil di muat ulang
     */
    public function refresh(): bool
    {
        if ($this->_id_hash != null) {
            // koneksi table data_kia_anak
            $this->PDO->query("SELECT * FROM `data_kia_anak` WHERE `id_hash` = :id_hash");
            $this->PDO->bind(":id_hash", $this->_id_hash, PDO::PARAM_STR);
            if ($this->PDO->single()) {
                // ambil data di table data_kia_anak
                $this->convertFromArray( $this->PDO->single() );
                return true;
            }
        }
        return false;
    }
    public function creat($id_hash): bool
    {
        // creat data biodata anak baru
        $this->PDO->query("INSERT INTO
                        `data_kia_anak`
                    (
                         `id`,
                         `id_hash`,
                         `tanggal_dibuat`,
                         `last_update`,
                         `jenis_kelamin`,
                         `bbl`,
                         `pbl`,
                         `kia`,
                         `imd`,
                         `asi_eks`,
                         `grups_posyandu`
                    )                        
                    VALUES (
                        :id,
                        :id_hash,
                        :tanggal_dibuat,
                        :last_update,
                        :jenis_kelamin,
                        :bbl,
                        :pbl,
                        :kia,
                        :imd,
                        :asi_eks,
                        :grups_posyandu
                    )");
        $this->PDO->bind(':id', '');
        $this->PDO->bind(':id_hash', $id_hash);
        $this->PDO->bind(':tanggal_dibuat', time());
        $this->PDO->bind(':last_update', time());
        $this->PDO->bind(':jenis_kelamin', $this->_jenis_kelamin);
        $this->PDO->bind(':bbl', $this->_bbl);
        $this->PDO->bind(':pbl', $this->_pbl);
        $this->PDO->bind(':kia', $this->_kia);
        $this->PDO->bind(':imd', $this->_imd);
        $this->PDO->bind(':asi_eks', $this->_asi_eks);
        $this->PDO->bind(':grups_posyandu', $this->_grups_posyandu);
        // menyimpan ke data base
        $this->PDO->execute();
        if ($this->PDO->rowCount() > 0) {
            return true;
        }
        return false;
    }
    /**
     * Menyipan data baru
     * @return bool True jika databerhasil disimpan data tidak ada data duplikat
     */
    public function update()
    {
        // prpare update biodata
        $this->PDO->query("UPDATE
                        `data_kia_anak`
                    SET 
                        `last_update` = :last_update,
                        `jenis_kelamin` = :jk,
                        `bbl` = :bbl,
                        `pbl` = :pbl,
                        `kia` = :kia,
                        `imd` = :imd,
                        `asi_eks` = :asi_eks,
                        `grups_posyandu` = :grups_posyandu
                    WHERE
                        `id_hash` = :id_hash
                    ");
        $this->PDO->bind(':last_update', time());
        $this->PDO->bind(':jk', $this->_jenis_kelamin);
        $this->PDO->bind(':bbl', $this->_bbl);
        $this->PDO->bind(':pbl', $this->_pbl);
        $this->PDO->bind(':kia', $this->_kia);
        $this->PDO->bind(':imd', $this->_imd);
        $this->PDO->bind(':asi_eks', $this->_asi_eks);
        $this->PDO->bind(':grups_posyandu', $this->_grups_posyandu);
        $this->PDO->bind(':id_hash', $this->_id_hash);
        // update biodata
        $this->PDO->execute();
        if ($this->PDO->rowCount() > 0) {
            return true;
        }
        return false;
    }
    public function cekExist(): bool
    {
        $this->PDO->query("SELECT `id_hash` FROM `data_kia_anak` WHERE `id_hash` = :id_hash");
        $this->PDO->bind(":id_hash", $this->_id_hash);
        if ($this->PDO->single()) {
            return true;
        }
        return false;
    }

    // data posyandu


    // konverter
    public function convertFromArray(array $data_kia): bool
    {
        $this->_jenis_kelamin   = $data_kia['jenis_kelamin'] ?? 1;
        $this->_bbl             = $data_kia['bbl'] ?? 0;
        $this->_pbl             = $data_kia['pbl'] ?? 0;
        $this->_kia             = $data_kia['kia'] ?? 0;
        $this->_imd             = $data_kia['imd'] ?? 0;
        $asi_eks                = $data_kia['asi_eks'] ?? 0;
        $this->_asi_eks         = $asi_eks == 'on' ? 1 : ($asi_eks == 1 ? 1 : 0);        // convert string to int value
        $this->_grups_posyandu  = $data_kia['grups_posyandu'] ?? 0;

        return true;                                                                    // todo: inputvalidate
    }
    public function convertToArray(): array
    {
        $data                   = [];
        $data['jenis_kelamin']  = $this->_jenis_kelamin;
        $data['bbl']            = $this->_bbl;
        $data['pbl']            = $this->_pbl;
        $data['kia']            = $this->_kia;
        $data['imd']            = $this->_imd;
        $data['asi_eks']        = $this->_asi_eks;
        $data['grups_posyandu'] = $this->_grups_posyandu;

        return $data;
    }
    
}
