<?php


namespace App\Classes\Traits;


trait FileStorage
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
       if ($this->file!=null) fclose( $this->file );
    }


    /**
     * Get all of the models from the file.
     *
     * @param  array|mixed  $columns
     * @return static[]
     */
    public static function all($columns = [])
    {
        return (new static)->fetch($columns);
    }


    /**
     * Filtering records and preparing the models fields
     *
     * @param  array|mixed  $columns
     * @return static[]
     */
    public function fetch($columns)
    {
        $models = [];

        foreach ($this->records as  $record){

            $model = $this->newModel();

            foreach (json_decode($record) as $param => $value){
                if ( in_array($param,$columns) || empty($columns) ) $model[$param] = $value;
            }

            $models[] = $model;
        }


        return $models;
    }


    /**
     * Create a new model
     *
     * @param  array  $attributes
     * @return static[]
     */
    public static function create(array $attributes = [])
    {
        return (new static)->createRecord($attributes);
    }


    /**
     * Creating and writing a new model in file
     *
     * @param  array  $attributes
     * @return static[]
     */
    public function createRecord(array $attributes) {
        $increment = 1;

        if (count($this->records) !== 0 ){
            $increment += json_decode( $this->records[ array_key_last( $this->records ) ] )->id; //Take id of the latest element in the list
        }

        $model = $this->newModel(array_merge(['id'=>$increment],$attributes));

        fwrite($this->file,json_encode($model->attributes).PHP_EOL);

        return $model;
    }


    /**
     * Update the model in the file.
     *
     * @param array $attributes
     * @param array $options
     * @return void
     */
    public function update(array $attributes = [], array $options = [])
    {
        $this->records[ $this->findIndex($this) ] = json_encode( array_merge( ["id" => $this->id], $attributes ) ).PHP_EOL;

        file_put_contents( $this->table_path , implode( PHP_EOL , $this->records)); //TODO:: return updated model
    }



    public function delete()
    {
        unset($this->records[ $this->findIndex($this) ]);

        file_put_contents( $this->table_path, implode( PHP_EOL , $this->records) );

        return json_encode(['success'=>'The '.$this->table.'. has been deleted']);
    }


    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $id
     * @param  string|null  $field
     * @return mixed
     */
    public function resolveRouteBinding($id, $field = null)
    {
        return $this->find($id);
    }


    /**
     * Create an instance of the class that uses the trait.
     *
     * @param array $attributes
     * @return mixed
     */
    private function newModel(array $attributes = [])
    {
        $className = get_class();
        $model = new $className();
        $model->attributes = $attributes;
        return $model;
    }


    /**
     * Find the model in the file and return as the instance of the class.
     *
     * @param int|string $id
     */
    private function find($id)
    {
        foreach ($this->records as $record){
            $model = $this->newModel( json_decode( $record,true ) );

            if ( $model->id === (int)$id )  return $model;
        }
    }


    /**
     * Find the index of the model in the file
     *
     * @param mixed $model
     */
    private function findIndex($model)
    {
        foreach ($this->records as $index=>$record){
            $_model = $this->newModel( json_decode( $record,true )) ;
            if ($_model->id === $model->id) return $index;

        }
    }
}
