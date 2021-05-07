<?php


namespace App\Classes\Traits;


use Illuminate\Support\Collection;

trait FileConcern
{
    protected $table;
    protected $table_path;
    protected $file;
    protected $records;

    public function __construct(){

        $this->table        = strtolower ( substr( strrchr( get_class() , '\\' ) ,1)); //Hate it too, but basically here I just cut the namespace and lowercase the class name

        $this->table_path   = storage_path('app/database/'.$this->table); //path to file with model data

        $this->file         = fopen( storage_path('app/database/' . $this->table),'a+' ) or die("Can't create '.$this->table.' table"); //prepare file

        $this->records      = file($this->table_path,FILE_IGNORE_NEW_LINES);
    }

    public function __destruct() {
        fclose( $this->file );
    }


    public static function all($columns = [])
    {
        return (new static)->fetchRecords($columns);
    }


    public function fetchRecords($columns)
    {
           return $this->fetch($columns);
    }

    public function fetch($columns) {
        $_records = [];

        foreach ($this->records as $index => $record){

            $className = get_class();
            $model = new $className;

            if ( in_array('id',$columns) || empty($columns) ) $model['id'] = $index;

            foreach (json_decode($record) as $param => $value){
                if ( in_array($param,$columns) || empty($columns) ) $model[$param] = $value;
            }

            $_records[] = $model;
        }


        return $_records;
    }

    public static function create(array $attributes = [])
    {
        dd(debug_backtrace());
//        return (new static)->createRecords($attributes);
    }



    public function update(array $attributes = [], array $options = [])
    {
        $this->records[$this->id] = json_encode( $attributes ) . PHP_EOL;

        file_put_contents( $this->table_path , implode( PHP_EOL, $this->records ) );

        foreach (json_decode($this->records[$this->id]) as $param=>$value){
            $this[$param] = $value;
        };

        return $this;
    }




    public function delete()
    {

    }


    public function resolveRouteBinding($key, $field = null)
    {
        if (array_key_exists($key, $this->records)){

            $this['id'] = $key;

            foreach (json_decode($this->records[$key]) as $param => $value){

                $this[$param] = $value;
            }

            return $this;
        }
    }


}
