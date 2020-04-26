<?php
/**
 * class ini berfungsi untuk mengirim pesan berupa tingkat kepuasan client
 * 
 * @author sonyprdana@gmail.com
 */
class Review extends Message{
    public function __construct($sender, $rating, $max_reating = 5, $taget = 'Rekam Medis')
    {
        $this->_sender =  $sender;
        $this->_resiver = 'sonypradana@gamil.com';
        $this->_type = 'review';
        $this->_message = '';
        $this->_date = time();
        $meta = sprintf('{"ver":"1.0", "maks-rating":"%d", "rating":"%d", "unit":"%s"', $max_reating, $rating, $taget);
        $this->_meta = $meta;
    }
}
