<?php
namespace Bonsai\Crud;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class CrudController extends Controller
{
    // protected $campos     = [];
    // protected $modelo     = null;
    // protected $titulo     = '';
    // protected $columnas   = [];
    // protected $breadcrumb = [];
    // protected $url        = '';
    // protected $relaciones = [];
    // protected $filesystem = 'null';
    // protected $botones    = [];
    protected $fields     = [];
    protected $model      = null;
    protected $title      = '';
    protected $columns    = [];
    protected $breadcrumb = [];
    protected $url        = '';
    protected $relations  = [];
    protected $filesystem = 'null';
    protected $buttons    = [];

    private function set_field($array)
    {
        $tmp = $array;

        if (!isset($array['editable'])) {
            $tmp['editable'] = true;
        }

        if (!isset($array['type'])) {
            $tmp['type'] = 'texto';
        }

        if (!isset($array['visible'])) {
            $tmp['visible'] = true;
        }

        if (isset($array['relation'])) {
            if (!in_array($array['relation'], $this->relations)) {
                $this->relations[] = $array['relation'];
            }
        }

        if ($tmp['type'] == 'url') {
            $url        = env('APP_URL') . $tmp['url'];
            $tmp['url'] = $url;
        }

        $this->fields[] = $tmp;
    }

    protected function render($params)
    {
        $this->model      = $params['model'];
        $this->title      = $params['title'];
        $this->breadcrumb = $params['breadcrumb'];
        $this->url        = $params['url'];
        $this->filesystem = (isset($params['filesystem']) ? $params['filesystem'] : 'public');
        $this->buttons    = (isset($params['buttons']) ? $params['buttons'] : []);
        $this->relations  = (isset($params['relations']) ? $params['relations'] : []);

        foreach ($params['fields'] as $campo) {
            $this->set_field($campo);
        }

        $fields        = collect($this->fields);
        $this->columns = $fields->filter(function ($fields) {
            return $fields['editable'];
        })
            ->pluck('column')
            ->toArray();
    }

    // === funciones del crud
    protected function index(Request $request)
    {
        $data = $this->model::query();

        if (!empty($this->relations)) {
            $data->with($this->relations);
        }

        $data = $data->get();

        $visible_fields = collect($this->fields)->filter(function ($field) {
            return $field['visible'];
        })
            ->toArray();

        return view('bonsaicrud::component')
            ->with('component', 'crud-index')
            ->with('title', $this->title)
            ->with('breadcrumb', $this->breadcrumb)
            ->with('params', [
                'fields'  => $visible_fields,
                'data'    => $data,
                'buttons' => $this->buttons,
                'url'     => $this->url,
            ]);
    }

    protected function create(Request $request)
    {
        return $this->edit($request, 0);
    }

    protected function edit(Request $request, $id)
    {
        return view('bonsaicrud::component')
            ->with('component', 'crud-edit')
            ->with('title', $this->title)
            ->with('breadcrumb', $this->breadcrumb)
            ->with('params', [
                'id'         => $id,
                'url'        => $this->url,
                'static_url' => config('services.static.url'),
            ]);
    }

    protected function update(Request $request, $id)
    {
        $data     = collect($request->data);
        $data_set = $data->only($this->columns)
            ->all();

        $relation_set = $data->only($this->relations)
            ->all();

        foreach ($relation_set as $relation => $values) {
            $campo = collect($this->fields)->filter(function ($campo) use ($relation) {
                return isset($campo['relation']) && $campo['relation'] == $relation;
            });

            if ($campo) {
                $campo = $campo->first();

                $data_set[$campo['columna']] = $values['id'];
            }
        }

        $final_data = [];
        foreach ($data_set as $column => $value) {
            if (!$value) {
                $value = 0;
            }

            if (is_array($value)) {
                $value = $value['id'];
            }

            if ($column == 'password') {
                if (strlen($value) < 20) {
                    $value = Hash::make($value);
                }
            }

            $final_data[$column] = $value;
        }

        if (isset($data['id'])) {
            $this->model::query()
                ->where('id', $data['id'])
                ->update($final_data);
        } else {
            $this->model::query()
                ->create($final_data);
        }

        return "saved";
    }

    protected function store(Request $request)
    {
        $file = $request->file('file');
        $path = $request->path;

        if ($file->isValid()) {
            $path = $request->file('file')->store($path, $this->filesystem);

            return response($path);
        }

        abort(501, 'Error al subir el archivo');
    }

    protected function destroy($id)
    {
        $this->model::query()
            ->where('id', $id)
            ->delete();

        return 'destroyed';
    }

    protected function show($id)
    {
        if ($id == 0) {
            $data = [];
            foreach ($this->columns as $column) {
                $data[$column] = '';
            }
        } else {
            $data = $this->model::query();

            if (!empty($this->relations)) {
                $data->with($this->relations);
            }

            $data = $data->findOrFail($id);
        }

        return response()->json([
            'fields' => $this->fields,
            'data'   => $data,
        ]);
    }
}
