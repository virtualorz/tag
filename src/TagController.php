<?php
namespace Virtualorz\Tag;

use DB;
use Request;
use Validator;
use App\Exceptions\ValidateException;
use PDOException;
use Exception;
use Pagination;
use Config;

class Tag
{
    public function list($page = 0) {

        $page_display = intval(Request::input('page_display', 10));
        if (!in_array($page_display, Config::get('pagination.data_display', []))) {
            $page_display = Config::get('pagination.items');
        }

        $qb = DB::table('tag')
            ->select([
                'tag.id',
                'tag.created_at',
                'tag.name',
                'tag.enable'
            ])
            ->whereNull('tag.delete');
        if($page !== 0)
        {
            $qb->offset(($page - 1) * $page_display)
                ->limit($page_display);
        }
        $dataSet = $qb->get();

        //多語言處理
        foreach($dataSet as $k=>$v)
        {
            $dataSet_lang = DB::table('tag_lang')
                ->select([
                    'tag_lang.lang',
                    'tag_lang.created_at',
                    'tag_lang.name',
                ])
                ->where('tag_lang.tag_id',$v->id)
                ->get()
                ->keyBy('lang');
            $dataSet[$k]->lang = $dataSet_lang;
        }
        $dataCount = $qb->cloneWithout(['columns', 'orders', 'limit', 'offset'])
                ->cloneWithoutBindings(['select', 'order'])
                ->count();
            
        Pagination::setPagination(['total'=>$dataCount]);

        return $dataSet;
    }

    public function add()
    {
        $validator = Validator::make(Request::all(), [
            'tag-name' => 'string|required|max:12',
            'tag-enable' => 'integer|required',
        ]);
        if ($validator->fails()) {
            throw new ValidateException($validator->errors());
        }

        foreach (Request::input('tag-lang', []) as $k => $v) {
            $validator = Validator::make($v, [
                'tag-name' => 'string|required|max:12',
            ]);
            if ($validator->fails()) {
                throw new ValidateException($validator->errors());
            }
        }

        $dtNow = new \DateTime();

        DB::beginTransaction();
        try {

            $insert_id = DB::table('tag')
                ->insertGetId([
                    'created_at' => $dtNow,
                    'updated_at' => $dtNow,
                    'name' => Request::input('tag-name'),
                    'enable' => Request::input('tag-enable'),
                    'creat_admin_id' => Request::input('tag-creat_admin_id', null),
                    'update_admin_id' => Request::input('tag-update_admin_id', null),
                ]);
            
            foreach (Request::input('tag-lang', []) as $k => $v) {
                DB::table('tag_lang')
                    ->insert([
                        'tag_id' => $insert_id,
                        'lang' => $k,
                        'created_at' => $dtNow,
                        'updated_at' => $dtNow,
                        'name' => $v['tag-name'],
                        'creat_admin_id' => Request::input('tag-creat_admin_id', null),
                        'update_admin_id' => Request::input('tag-update_admin_id', null),
                    ]);
            }

            DB::commit();

        } catch (\PDOException $ex) {
            DB::rollBack();
            throw new PDOException($ex->getMessage());
            \Log::error($ex->getMessage());
        } catch (\Exception $ex) {
            DB::rollBack();
            throw new Exception($ex);
            \Log::error($ex->getMessage());
        }
    }

    public function edit()
    {
        $validator = Validator::make(Request::all(), [
            'tag-name' => 'string|required|max:12',
            'tag-enable' => 'integer|required',
        ]);
        if ($validator->fails()) {
            throw new ValidateException($validator->errors());
        }

        foreach (Request::input('tag-lang', []) as $k => $v) {
            $validator = Validator::make($v, [
                'tag-name' => 'string|required|max;12',
            ]);
            if ($validator->fails()) {
                throw new ValidateException($validator->errors());
            }
        }

        $dtNow = new \DateTime();

        DB::beginTransaction();
        try {
            
            DB::table('tag')
                ->where('id', Request::input('id'))
                ->update([
                    'updated_at' => $dtNow,
                    'name' => Request::input('tag-name'),
                    'enable' => Request::input('tag-enable'),
                    'update_admin_id' => Request::input('tag-update_admin_id', null),
                ]);
            foreach (Request::input('tag-lang', []) as $k => $v) {
                DB::table('tag_lang')
                    ->where('tag_id', Request::input('id'))
                    ->where('lang', $k)
                    ->update([
                        'updated_at' => $dtNow,
                        'name' => $v['tag-name'],
                        'update_admin_id' => Request::input('tag-update_admin_id', null),
                    ]);
            }

            DB::commit();

        } catch (\PDOException $ex) {
            DB::rollBack();
            throw new PDOException($ex->getMessage());
            \Log::error($ex->getMessage());
        } catch (\Exception $ex) {
            DB::rollBack();
            throw new Exception($ex);
            \Log::error($ex->getMessage());
        }
    }

    public function detail($id = '')
    {
        $dataRow_tag = collect();
        try {
            $dataRow_tag = DB::table('tag')
                ->select([
                    'tag.id',
                    'tag.created_at',
                    'tag.updated_at',
                    'tag.name',
                    'tag.enable',
                    'tag.update_admin_id',
                ])
                ->where('tag.id', $id)
                ->whereNull('tag.delete')
                ->first();
            if ($dataRow_tag != null) {
                $dataSet_lang = DB::table('tag_lang')
                    ->select([
                        'tag_lang.lang',
                        'tag_lang.created_at',
                        'tag_lang.updated_at',
                        'tag_lang.name',
                    ])
                    ->where('tag_lang.tag_id', $dataRow_tag->id)
                    ->get()
                    ->keyBy('lang');
                $dataRow_tag->lang = $dataSet_lang;
            }
        } catch (\PDOException $ex) {
            throw new PDOException($ex->getMessage());
            \Log::error($ex->getMessage());
        } catch (\Exception $ex) {
            throw new Exception($ex);
            \Log::error($ex->getMessage());
        }

        return $dataRow_faq;
    }

    public function delete()
    {
        $validator = Validator::make(Request::all(), [
            'id' => 'required', //id可能是陣列可能不是
        ]);
        if ($validator->fails()) {
            throw new ValidateException($validator->errors());
        }

        $ids = Request::input('id', []);
        if (!is_array($ids)) {
            $ids = [$ids];
        }

        $dtNow = new \DateTime();

        DB::beginTransaction();
        try {
            foreach ($ids as $k => $v) {

                DB::table('tag')
                    ->where('id', $v)
                    ->update([
                        'delete' => $dtNow,
                    ]);
            }

            DB::commit();
        } catch (\PDOException $ex) {
            DB::rollBack();
            throw new PDOException($ex->getMessage());
            \Log::error($ex->getMessage());
        } catch (\Exception $ex) {
            DB::rollBack();
            throw new Exception($ex);
            \Log::error($ex->getMessage());
        }
    }

    public function enable($type = '')
    {
        if ($type !== '') {
            $validator = Validator::make(Request::all(), [
                'id' => 'required', //id可能是陣列可能不是
            ]);
            if ($validator->fails()) {
                throw new ValidateException($validator->errors());
            }

            $ids = Request::input('id', []);
            if (!is_array($ids)) {
                $ids = [$ids];
            }

            $dtNow = new \DateTime();

            DB::beginTransaction();
            try {
                foreach ($ids as $k => $v) {
                    DB::table('tag')
                        ->where('id', $v)
                        ->whereNull('delete')
                        ->update([
                            'enable' => $type,
                            'updated_at' => $dtNow,
                        ]);
                }
                DB::commit();
            } catch (\PDOException $ex) {
                DB::rollBack();
                throw new PDOException($ex->getMessage());
                \Log::error($ex->getMessage());
            } catch (\Exception $ex) {
                DB::rollBack();
                throw new Exception($ex);
                \Log::error($ex->getMessage());
            }
        }
    }
}
