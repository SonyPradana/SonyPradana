<?php
/**
 * class ini berfungsi untuk mengirim respone client terhadap website
 */
class ContactUs extends Message{
    /** 
     * membuat pesan baru dengan format: pengirim, isi pesan dan jenis pesan
     * 
     * @param string $sender pengirim pesan (email)
     * @param string $message isi pesan (mak 257)
     * @param string $regarding jenis pesan (contoh: bug, information, dln)
     */
    public function __construct($sender, $message, $regarding = 'review'){
        $this->_sender = $sender;
        $this->_resiver = 'sonypradana@gmail.com'; // akun admin
        $this->_type = 'contact';
        $this->_message = $message;
        $this->_date = time();
        $meta = sprintf('{"ver":"1.0", "Regarding":"%s"}', $regarding);
        $this->_meta = $meta;
    }
}

