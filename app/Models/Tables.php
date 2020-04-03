<?php
namespace App\Models;

class Tables extends Database
{
    //CREATE TABLES AND ADD DEFAULT STOCKS IN DATABASE
    public function initialize_database()
    {
        try {

            //Create tables
            $query = $this->db->query(
                'CREATE TABLE `users` (
                `user_id` INT NOT NULL AUTO_INCREMENT UNIQUE,
                `username` varchar(255) NOT NULL UNIQUE,
                `password` varchar(255) NOT NULL,
                `disabled` BOOLEAN NOT NULL,
                `disabled_reason` varchar(255),
                `create_date` TIMESTAMP NOT NULL,
                PRIMARY KEY (`user_id`)
            )'
            );

            $query = $this->db->query(
                'CREATE TABLE `issuers` (
                `issuer_id` INT NOT NULL AUTO_INCREMENT UNIQUE,
                `issuer_name` varchar(255) NOT NULL UNIQUE,
                `issuer_desc` TEXT NOT NULL,
                `sponsored` BOOLEAN NOT NULL,
                PRIMARY KEY (`issuer_id`)
            )'
            );

            $query = $this->db->query(
                'CREATE TABLE `stonks` (
                `stonk_id` INT NOT NULL AUTO_INCREMENT,
                `stonk_name` varchar(255) NOT NULL,
                `issuer_id` INT NOT NULL,
                `stonk_desc` TEXT NOT NULL,
                `stonk_tradable` BOOLEAN NOT NULL,
                `volatility` INT,
                `base` INT,
                PRIMARY KEY (`stonk_id`),
                FOREIGN KEY (`issuer_id`) REFERENCES issuers(issuer_id)
            )'
            );

            $query = $this->db->query(
                'CREATE TABLE `transactions` (
                `tx_id` INT NOT NULL AUTO_INCREMENT UNIQUE,
                `user_id` INT NOT NULL ,
                `tx_value` INT NOT NULL,
                `tx_type` varchar(255),
                `stonk_id` INT NOT NULL,
                `stonk_amount` INT NOT NULL,
                `tx_date` TIMESTAMP NOT NULL,
                PRIMARY KEY (`tx_id`),
                FOREIGN KEY (`user_id`) REFERENCES users(user_id),
                FOREIGN KEY (`stonk_id`) REFERENCES stonks(stonk_id)
            )'
            );

            //Add default issuer and dummy stonk for money transactions
            $query = $this->db->query(
                'INSERT INTO issuers
                (issuer_id, issuer_name, issuer_desc, sponsored) VALUES
                (1, "Default Issuer", "Default Issuer", false)'
            );

            $query = $this->db->query(
                'INSERT INTO stonks
                (stonk_name, issuer_id, stonk_desc, stonk_tradable) VALUES
                ("", 1, "", false)'
            );

            //Add default issuers and a few stonks
            $default_issuer_names = ['ACME Company LTD', 'Worldwide Trading Co', 'United Refineries'];
            $default_stonks = ['ACM', 'WTC', 'UNR'];
            $default_issuer_desc = 'A vibrant and well-respected company in their field.';
            $default_stonk_desc = 'Good choice for beginner traders.';

            for ($i = 0; $i < count($default_issuer_names); $i++) {
                $query = $this->db->query(
                    'INSERT INTO issuers
                    (issuer_name, issuer_desc, sponsored) VALUES
                    ("' . $default_issuer_names[$i] . '", "' . $default_issuer_desc . '", false)'
                );

                $query = $this->db->query('SELECT LAST_INSERT_ID() AS issuer_id');
                $issuer_id = $query->getRow()->issuer_id;

                for ($j = rand(1, 5); $j > 0; $j--) {
                    $query = $this->db->query(
                        'INSERT INTO stonks 
                        (stonk_name, issuer_id, stonk_desc, stonk_tradable, base, volatility) VALUES
                        ("' . $default_stonks[$i] . ' No. ' . $j . '", ' . $issuer_id . ', "' . $default_stonk_desc . '", true, ' . rand(1, 100) . ', ' . rand(1, 100) . ')'
                    );
                }
            }
        } catch (\Throwable $th) {
            return false;
        }
        return true;
    }

    public function drop_db_tables()
    {
        try {
            $query = $this->db->query('DROP TABLE transactions');
            $query = $this->db->query('DROP TABLE stonks;');
            $query = $this->db->query('DROP TABLE issuers');
            $query = $this->db->query('DROP TABLE users');
        } catch (\Throwable $th) {
            return false;
        }
        return true;
    }
}
