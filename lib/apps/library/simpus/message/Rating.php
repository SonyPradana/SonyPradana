<?php

namespace Simpus\Message;

/**
 * class ini berfungsi untuk mengirim pesan berupa tingkat kepuasan client
 * 
 * @author sonyprdana@gmail.com
 */
class Rating extends Message{
    /**
     * menilai tingkat kepuasan client / pasien
     * 
     * @param string $sender pengirim (email, ip adress)
     * @param integer $rating rating yang diberikan pelanggan
     * @param integer $max_rating nilai tertinggi dalam rating
     * @param string $target unit bagian yang dinilai
     */
    public function __construct($sender, $rating, $max_rating = 5, $target = 'Rekam Medis')
    {
        // senitilizer input
        $max_rating = $max_rating < 0 ? 1 : $max_rating;
        $rating = $rating > $max_rating ? $max_rating : $rating;
        $rating= $rating < 1 ? 1 : $rating;

        $this->_sender =  $sender;
        $this->_resiver = 'sonypradana@gmail.com';
        $this->_type = 'review';
        $this->_message = '';
        $this->_date = time();
        $meta = sprintf('{"ver":"1.0", "maks-rating":"%d", "rating":"%d", "unit":"%s"}', $max_rating, $rating, $target);
        $this->_meta = $meta;
    }
}
