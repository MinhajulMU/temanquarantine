<?php

namespace App\Models\Common;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use App\Uuids;

class BaseModel extends Model
{
	use SoftDeletes, Uuids;

	// untuk model yang menggunakan composite key
	protected $compositeKeys = [];

	// apabila ada kolom yang tidak boleh diupdate secara masif
	protected $guarded = [];

	// selalu pakai timestamp (created_at dan updated_at), otomatis
	public $timestamps = true;

	// desc kolom untuk combo
	protected $descColumns = [];

	// disable increment karena kita pakai GUID
	public $incrementing = false;

	// rules untuk validasi sebelum data dibuat/diupdate
	// https://laravel.com/docs/5.7/validation#available-validation-rules
	public $rules = [];

	// apakah primary key otomatis? (GUID)
	public $isAutoPrimary = true;

	// apakah sudah diubah manual? Supaya tidak tertimpa
	public $isCustom = false;

	// apakah ada file yang terkait dengan model ini?
	public $hasFile = false;

	// apakah multiple file?
	public $multipleFile = false;

	// rules untuk dokumen
	public $rules_dokumen = 'required|mimes:pdf,jpg,png|max:2000';

	/**
	 * @return array
	 */
	public function getCompositeKeys()
	{
		return $this->compositeKeys;
	}

	public function getDescColumns()
	{
		return $this->descColumns;
	}

	public function getPrimaryKey()
	{
		return $this->primaryKey;
	}

	/**
	 * Fungsi ini untuk mengakomodasi model yang menggunakan composite key
	 * @param Builder
	 */
	protected function setKeysForSaveQuery(Builder $query)
    {
    	if(count($this->compositeKeys) > 0)
    	{
    		foreach($this->compositeKeys as $pk)
    		{
	        	$query->where($pk, '=', $this->getAttribute($pk));
    		}
	    }
	    else
	    {
			#$query->where($this->getKeyName(), '=', $this->getKeyForSaveQuery());
			$query = parent::setKeysForSaveQuery($query);
	    }
        return $query;
	}

	/**
	 * Fungsi ini untuk mendapatkan data leaf dari TREE 
	 * @param GUID Induk
	 */

	public function getTreeLeaf($induk=null,$params=array())
	{
		$pk = $this->primaryKey;
		$q = \DB::table($this->table);
		$q->whereNull('deleted_at');
		if(is_array($params))
		{
			foreach($params as $k => $v)
			{
				$q->where($k,$v);
			}
		}
		if(!is_null($induk) && $induk)
		{
			$q->where('induk',$induk);
		}
		$q = $q->get();

		$units = [];
		foreach($q as $row)
		{
			// cek child
			$num_child = \DB::table($this->table)
				->whereNull('deleted_at')
				->where('induk',$row->$pk)
				->count();
			if($num_child == 0)
			{
				$val = [];
				foreach($this->getDescColumns() as $dc)
				{
					$val[] = $row->$dc;
				}
				$units[$row->$pk] = implode(" - ",$val);
			}
			else
			{
				$units = array_merge($units,$this->getTreeLeaf($row->$pk,$params)); 
			}
		}
		return $units;
	}

	/**
	 * Fungsi ini untuk mendapatkan data TREE dari parameter ID CHILD (ke atas)
	 * @param GUID ID
	 */

	public function getTreeUp($id)
	{
		$row = \DB::table($this->table)
			->whereNull('deleted_at')
			->where($this->primaryKey,$id)
			->first();

		$val = [];
		foreach($this->getDescColumns() as $dc)
		{
			$val[] = $row->$dc;
		}
		$pk = $this->primaryKey;
		$units[$row->$pk] = [implode(" - ",$val),$row];

		if($row->induk != "")
		{
			return array_merge($units,$this->getTreeUp($row->induk));
		}
		else
		{
			return $units;
		}
	}

	/**
	 * Fungsi ini untuk mendapatkan data TREE dari parameter ID PARENT (ke bawah)
	 * @param GUID ID
	 * @param $params untuk filtering
	 * @param $is_force_filter apakah force filter di setiap level?
	 */

	public function getTreeDown($id=null,$params=array(),$is_force_filter=0)
	{
		$pk = $this->primaryKey;
		$q = \DB::table($this->table);
		$q->whereNull('deleted_at');
		if($id)
		{
			$q->where('induk',$id);
		}
		else
		{
			$q->whereNull('induk');
		}
		if($is_force_filter && $params)
		{
			if(is_array($params))
			{
				foreach($params as $k => $v)
				{
					$q->where($k,$v);
				}
			}
		}
		$q = $q->get();

		$units = [];
		foreach($q as $row)
		{
			if($is_force_filter == 0 && $params)
			{
				if(count($params) > 0)
				{
					foreach($params as $k => $v)
					{
						if($row->$k != $v)
						{
							$units = array_merge($units,$this->getTreeDown($row->$pk,$params,$is_force_filter));
							continue 2;
						}
					}
				}
			}
			// memenuhi
			$val = [];
			foreach($this->getDescColumns() as $dc)
			{
				$val[] = $row->$dc;
			}

			$units[$row->$pk] = [implode(" - ",$val),$row];

			$units += $this->getTreeDown($row->$pk,$params,$is_force_filter);
		}
		return $units;
	}

	public function hapusData($id)
	{
		$model = $this->find($id);

        if(is_null($model))
        {
            return array(false,"Data not found");
        }
        else
        {
        	$model->delete();       	
            return array(true,"Data sukses dihapus");
        }
	}

	public function getDataById($id)
	{
		return $this->find($id);
	}

	public function updateData($request,$model,$data,$data_dokumen=false,$id)
	{
		$model->update($data);

		if(isset($data_dokumen) && $data_dokumen)
		{
			$data_dokumen['model_id'] = $id;
			$this->saveDokumen($request->file('dokumen'),$data_dokumen,false);
		}
		return $model;
	}

	public function simpanData($request,$data,$data_dokumen=false)
	{
		$primary = $this->getPrimaryKey();
		$done = false;

		# && !is_array($primary) && isset($data[$primary])
		if($this->isAutoPrimary == false)
		{
			$cek = null;
			// cek apakah sudah ada tapi soft deleted
			$composite = $this->getCompositeKeys();
			if(count($composite) > 0)
			{
				// ada composite keynya
				$key_ok = true;
				foreach($composite as $key)
				{
					if(!isset($data[$key]))
					{
						$key_ok = false;
					}
				}
				if($key_ok)
				{
					$cek = $this->withTrashed();
					foreach($composite as $key)
					{
						$cek->where($key,$data[$key]);
					}
					$cek = $cek->first();
				}
			}
			elseif(isset($data[$primary]))
			{
				$cek = $this->withTrashed()->find($data[$primary]);
			}

			if(!is_null($cek))
			{
				$cek->restore()->update($data);
				$done = true;
				$model = $cek;
			}
		}
		if($done == false)
			$model = $this->create($data);

		if(isset($data_dokumen) && $data_dokumen)
		{
			$data_dokumen['model_id'] = $model->$primary;
			$this->saveDokumen($request->file('dokumen'),$data_dokumen,false);
		}
		return $model;
	}

	public function pre_process($data)
	{
		$rules = $this->rules;
		foreach($rules as $k => $v)
		{
			if(stristr($v,"numeric") !== false)
			{
				if(isset($data[$k]) && $data[$k] != "")
					$data[$k] = str_replace(",","",$data[$k]);
			}
		}
		return $data;
	}

	public function getRelationInfo()
	{
		$q = \DB::select("select * from information_schema.KEY_COLUMN_USAGE where TABLE_SCHEMA='".env('DB_DATABASE')."' and TABLE_NAME='".$this->table."' and REFERENCED_TABLE_NAME is not null");

		$ret = [];
		foreach($q as $row)
		{
			$mdl = "\\App\\Models\\Common\\".ucfirst(camel_case($row->REFERENCED_TABLE_NAME));
			$mdl = new $mdl;
			$ret[$row->COLUMN_NAME] = [$row->REFERENCED_TABLE_NAME,$row->REFERENCED_COLUMN_NAME,$mdl->getDescColumns()];
		}
		return $ret;
	}
	
	/**
	* List data komplit  
	* @param  $perpage Per halaman
	* @return array object hasil pencarian
	*/
	public function getListData($perpage=0)
	{
		$relations = $this->getRelationInfo();

		$q = \DB::table($this->table." as a");
		$q->select('a.*');
		foreach($relations as $col => $arr)
		{
			#$q->leftJoin($arr[0],$arr[0].".".$arr[1],"=","a.".$col);

			$q->leftJoin($arr[0]." as ".$arr[0]."_".$col,$arr[0]."_".$col.".".$arr[1],"=","a.".$col);

			foreach($arr[2] as $desc)
			{
				$q->addSelect(\DB::raw($arr[0]."_".$col.".".$desc." as ".$col));
				break;
			}
		}
		$q->whereNull('a.deleted_at');
		if($perpage > 0)
			return $q->paginate($perpage);
		return $q->get();
	}

    /**
     * @param  $keyword berupa array pencarian
     * @return array object hasil pencarian
     */
	public function searchByKeywords($keywords,$perpage=0)
	{
		$orderBy = array();
		$with_foreign = false;

		$q = \DB::table($this->table." as a");

		$q->whereNull('a.deleted_at');

		$q->where(function($w) use ($keywords,&$orderBy,&$with_foreign){
			foreach($keywords as $k => $v)
			{
				if($k == "query")
				{
					$desc = $this->descColumns;

					foreach($desc as $dc)
					{
						$w->orWhere("a.".$dc,'like','%'.$v.'%');
					}
				}
				elseif($k == "orderBy")
				{
					$ex_obb = explode("#",$v);
					foreach($ex_obb as $v)
					{
						$ex_ob = explode(",",$v);
						if(count($ex_ob) == 1)
							$ex_ob[1] = "asc";
						$orderBy["a.".$ex_ob[0]] = $ex_ob[1];
					}
				}
				elseif($k == "withForeign")
				{
					$with_foreign = true;
				}
				else
				{
					if($v == "-1" && $k == "induk")
					{
						$w->whereNull("a.".$k);
					}
					else
					{
						$w->where("a.".$k,$v);
					}
				}
			}
		});

		if($perpage > 0 || $with_foreign)
		{
			$relations = $this->getRelationInfo();

			$q->select('a.*');
			foreach($relations as $col => $arr)
			{
				$q->leftJoin($arr[0]." as ".$arr[0]."_".$col,$arr[0]."_".$col.".".$arr[1],"=","a.".$col);

				foreach($arr[2] as $desc)
				{
					$q->addSelect(\DB::raw($arr[0]."_".$col.".".$desc." as ".$col));
					$q->addSelect(\DB::raw("a.".$col." as raw_".$col));
					break;
				}
			}
		}

		if(isset($orderBy))
		{
			foreach($orderBy as $k => $v)
			{
				$q->orderBy($k,$v);
			}
		}

		if($perpage > 0)
			return $q->paginate($perpage);
		return $q->get();
	}

	/**
     * @param  $keyword berupa array pencarian
     * @return array combo siap pakai
     */
	public function getCombo($keyword='')
	{
		$q = \DB::table($this->table);
		if($keyword)
		{
			if(is_array($keyword))
			{
				foreach($keyword as $k => $v)
				{
					if(is_array($v))
					{
						if(count($v) > 0)
						{
							$q->whereIn($k,$v);
						}
					}
					else
					{
						$q->where($k,$v);
					}
				}
			}
			else
			{
				foreach($this->descColumns as $col)
				{
					$q->where(function($w) use ($col,$keyword){
						$w->orWhere($col,'like','%'.$keyword.'%');
					});
				}
			}
		}
		$q->whereNull('deleted_at');
		$q = $q->get();

		$data = array();

		foreach($q as $row)
		{
			$desc = [];
			foreach($this->descColumns as $col)
			{
				$desc[] = $row->$col;
			}
			$pri = $this->primaryKey;
			$data[$row->$pri] = implode(" - ",$desc);
		}

		return $data;
	}

	public function saveDokumen($file,$data,$is_multiple=true)
	{
		$dokumen = new \App\Models\Common\Dokumen;

        $status = true;
        $model = null;
        $message = "";
        try 
        {
            // simpan di dalam filesystem
            $basepath = config('cnf.basepath_file_upload');

            $full_path = get_file_upload_path($basepath);
            $file_path = str_replace($basepath,"",$full_path);

            // proses pemindahan file
            $mimetype = $file->getClientMimeType();
            $size = $file->getClientSize();
            $filename = time()."_".$file->getClientOriginalName();
            $data = array_merge($data,array(
                'file_path'	=> $file_path,
                'file_name' => $filename,
                'file_type'	=> $mimetype,
                'file_size' => round($size/1024,2),
            ));
            $file->move($full_path,$filename);

			if($is_multiple)
			{
				$model = $dokumen::create($data);                
			}
			else
			{
				$model = $dokumen->where('model',$data['model'])
					->where('model_id',$data['model_id'])
					->whereNull('deleted_at')
					->first();
				if(is_null($model))
				{
					$model = $dokumen::create($data);
				}
				else
				{
					$model = $model->update($data);
				}
			}

            $message = "Dokumen sukses disimpan";
        } 
        catch (\Illuminate\Database\QueryException $e) 
        {
            $status = false;
            $message = $e->getMessage();
        } 
        catch (PDOException $e) 
        {
            $status = false;
            $message = $e->getMessage();
		}
		
		return [$status,$message,$model];
	}
}