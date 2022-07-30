<?php
namespace Bonsai\Crud;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class CrudController extends Controller
{
    protected $campos     = [];
    protected $modelo     = null;
    protected $titulo     = '';
    protected $columnas   = [];
    protected $breadcrumb = [];
    protected $url        = '';
    protected $relaciones = [];
    protected $filesystem = 'null';
    protected $botones    = [];

    private function set_campo($array)
    {
        $tmp = $array;

        if (!isset($array['editable'])) {
            $tmp['editable'] = true;
        }

        if (!isset($array['tipo'])) {
            $tmp['tipo'] = 'texto';
        }

        if (!isset($array['visible'])) {
            $tmp['visible'] = true;
        }

        if (isset($array['relacion'])) {
            if (!in_array($array['relacion'], $this->relaciones)) {
                $this->relaciones[] = $array['relacion'];
            }
        }

        if ($tmp['tipo'] == 'url') {
            $url        = env('APP_URL') . $tmp['url'];
            $tmp['url'] = $url;
        }

        $this->campos[] = $tmp;
    }

    protected function render($params)
    {
        $this->modelo     = $params['modelo'];
        $this->titulo     = $params['titulo'];
        $this->breadcrumb = $params['breadcrumb'];
        $this->url        = $params['url'];
        $this->filesystem = (isset($params['filesystem']) ? $params['filesystem'] : 'public');
        $this->botones    = (isset($params['botones']) ? $params['botones'] : []);
        $this->relaciones = (isset($params['relaciones']) ? $params['relaciones'] : []);

        foreach ($params['campos'] as $campo) {
            $this->set_campo($campo);
        }

        $campos         = collect($this->campos);
        $this->columnas = $campos->filter(function ($campos) {
            return $campos['editable'];
        })
            ->pluck('columna')
            ->toArray();
    }

    // === funciones del crud
    protected function index(Request $request)
    {
        $datos = $this->modelo::query();

        if (!empty($this->relaciones)) {
            $datos->with($this->relaciones);
        }

        $datos = $datos->get();

        $campos_visibles = collect($this->campos)->filter(function ($campo) {
            return $campo['visible'];
        })
            ->toArray();

        return view('bonsaicrud::component')
            ->with('component', 'crud-index')
            ->with('titulo', $this->titulo)
            ->with('breadcrumb', $this->breadcrumb)
            ->with('params', [
                'campos'  => $campos_visibles,
                'datos'   => $datos,
                'botones' => $this->botones,
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
            ->with('titulo', $this->titulo)
            ->with('breadcrumb', $this->breadcrumb)
            ->with('params', [
                'id'         => $id,
                'url'        => $this->url,
                'static_url' => config('services.static.url'),
            ]);
    }

    protected function update(Request $request, $id)
    {
        $data     = collect($request->dato);
        $data_set = $data->only($this->columnas)
            ->all();

        $relaciones_set = $data->only($this->relaciones)
            ->all();

        foreach ($relaciones_set as $relacion => $valores) {
            $campo = collect($this->campos)->filter(function ($campo) use ($relacion) {
                return isset($campo['relacion']) && $campo['relacion'] == $relacion;
            });

            if ($campo) {
                $campo = $campo->first();

                $data_set[$campo['columna']] = $valores['id'];
            }
        }

        $final_data = [];
        foreach ($data_set as $columna => $valor) {
            if (!$valor) {
                $valor = 0;
            }

            if (is_array($valor)) {
                $valor = $valor['id'];
            }

            if ($columna == 'password') {
                if (strlen($valor) < 20) {
                    $valor = Hash::make($valor);
                }
            }

            $final_data[$columna] = $valor;
        }

        if (isset($data['id'])) {
            $this->modelo::query()
                ->where('id', $data['id'])
                ->update($final_data);
        } else {
            $this->modelo::query()
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
        $this->modelo::query()
            ->where('id', $id)
            ->delete();

        return 'destroyed';
    }

    protected function show($id)
    {
        if ($id == 0) {
            $dato = [];
            foreach ($this->columnas as $columna) {
                $dato[$columna] = '';
            }
        } else {
            $dato = $this->modelo::query();

            if (!empty($this->relaciones)) {
                $dato->with($this->relaciones);
            }

            $dato = $dato->findOrFail($id);
        }

        return response()->json([
            'campos' => $this->campos,
            'dato'   => $dato,
        ]);
    }
}
