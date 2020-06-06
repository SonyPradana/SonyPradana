<?php
    $ungaran_barat = [
        "branjang" => [
            1 => "branjang",
            2 => "truko",
            3 => "cemanggah lor",
            4 => "cemanggah kidul",
            5 => "dersune"
        ],
        "bandarjo" => [
            1 => "bandarjo"
        ],
        "kalisidi" => [
            1 => "kalisidi"
        ],
        "keji" => [
            1 => "keji"
        ],
        "lerep" => [
            1 => "indrokilo",
            4 => "soko",
            5 => "tegal rejo",
            6 => "lorog",
            8 => "kretek",
            10 => "mapagan"
        ],
        "nyatnyono" => [
            1 => "nyatnono"
        ]
    ];

    header_remove("Expires");
    header_remove("Pragma");
    header_remove("X-Powered-By");
    header_remove("Connection");
    header_remove("Server");
    header("Cache-Control:	private");
    header("Content-Type: application/json; charset=utf-8");

    $result = [
        "status" => "ok",
        "kecamatan" => "ungaran barat",
        "data" => $ungaran_barat
    ];

    echo json_encode($result);
