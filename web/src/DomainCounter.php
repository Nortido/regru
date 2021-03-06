<?php
/**
 * @author Evgeny Novoselov <nortido@gmail.com>
 */

namespace App;


use App\Exceptions\DieException;

class DomainCounter
{
    /**
     * @var     array
     */
    public $domains = [];

    /**
     * @param   string $filename
     * @throws  DieException
     */
    function run( string $filename )
    {
        $file = FileReader::read_file( $filename );

        $domains_stat = $this->process_all_emails( $file );

        $this->show_emails_info( $domains_stat );
    }

    /**
     * @param   array $emails
     * @return  array
     * @throws  DieException
     */
    function process_all_emails( array $emails ) : array
    {
        $this->check_emails_array( $emails );

        foreach ( $emails as $email ) {
            $domain = Email::get_domain( $email );

            $this->domains = $this->count_domain( $domain, $this->domains );
        }
        $this->domains = $this->sort_domains( $this->domains );

        return $this->domains;
    }

    /**
     * @param   string $domain
     * @return  array
     */
    function count_domain( string $domain, array $domains ) : array
    {
        # Search domain in domains array
        # Add new if not found
        if ( !in_array( $domain, array_keys( $domains ) ) ) {
            $domains[ $domain ] = 1;
        }
        # Increment counter if domain already exists
        else {
            $domains[ $domain ]++;
        }

        return $domains;
    }

    /**
     * @param   array $domains
     * @return  array
     */
    function sort_domains( array $domains ) : array
    {
        # Separate invalid domains
        if ( key_exists('INVALID', $domains ) ) {
            $invalids = $domains[ 'INVALID' ];
            unset( $domains[ 'INVALID' ] );
        }

        # Sorting domains array by value
        arsort($domains );

        # Add invalid item back if exist
        if ( isset( $invalids ) ) {
            $domains[ 'INVALID' ] = $invalids;
        }

        return $domains;
    }

    /**
     * @param   array $emails
     * @throws  DieException
     */
    function show_emails_info( array $emails )
    {
        $this->check_emails_array( $emails );

        foreach ( $emails as $domain => $value ) {
            echo $domain . "\t" . $value . PHP_EOL;
        }
    }

    /**
     * @param   array $emails
     * @throws  DieException
     */
    function check_emails_array( array $emails )
    {
        if ( count( $emails ) == 0) {
            throw new DieException( "No emails to process" );
        }
    }
}
