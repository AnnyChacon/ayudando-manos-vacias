<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Nino;
use App\Localidad;
use App\Cancer;
use App\Medicamento;
use App\Categoria_Insumo;
use App\Nino_Cancer;
use Carbon\Carbon;

class NinoController extends Controller
{
    public function buscarNinos(Request $request)
    {
        $ninos = Nino::where(function ($query){
            $query->has('medicamentosRequeridos')->orHas('insumosRequeridos');
        })->where(function($query) use ($request){
            //filtrar por cancer
            if($request->exists('cancer') && $request['cancer']){
                $query->whereHas('canceres', function($q) use ($request){
                    $q->where('cancer_id', $request['cancer']);
                });
            }
            //filtrar por medicamento
            if($request->exists('medicamentos') && $request['medicamentos']){
                $query->whereHas('medicamentosRequeridos', function($q) use ($request){
                    $q->where('medicamento_id', $request['medicamentos']);
                });
            }
            //filtrar por categoria de insumo
            if($request->exists('insumos') && $request['insumos']){
                $query->whereHas('insumosRequeridos', function($q) use ($request){
                    $q->where('categoria_insumo_id', $request['insumos']);
                });
            }
            //filtrar por estado
            if($request->exists('estado') && $request['estado'] != 'Estado'){
                $query->whereHas('representante.localidad', function($q) use ($request){
                    $q->where('localidad_id', $request['estado']);
                });
            }
            //filtrar por municipio
            if($request->exists('municipio') && $request['municipio'] != 'Municipio'){
                $query->whereHas('representante', function($q) use ($request){
                    $q->where('localidad_id', $request['municipio']);
                });
            }
        })->paginate(10);

        $estados = Localidad::whereNull('localidad_id')->orderBy('nombre')->get();
        $municipios = [];
        if($request['estado'])
        {
            $municipios = Localidad::where('localidad_id', $request['estado'])->orderBy('nombre')->get();
        }
        $canceres = Cancer::orderBy('nombre')->get();
        $medicamentos = Medicamento::orderBy('nombre')->get();
        $insumos_cat = Categoria_Insumo::orderBy('nombre')->get();
        return view('buscar_nino', [
            'ninos' => $ninos, 
            'estados' => $estados, 
            'municipios' => $municipios, 
            'canceres' => $canceres, 
            'medicamentos' => $medicamentos, 
            'insumos_cat' => $insumos_cat, 
            'old' => $request
        ]);
    }

    public function registroNino()
    {
        $canceres = Cancer::orderBy('nombre')->get();
        return view('registro_nino', ['canceres' => $canceres, 'relacionesRepr' => Nino::$relacionesRepr]);
    }

    public function registrarNino(Request $request)
    {
        $this->validate($request, [
            'nombre' => 'required',
            'apellido' => 'required',
            'fecha_nacimiento' => 'required|date_format:Y-m-d',
            'genero' => 'required',
            'identificacion' => 'nullable',
            'situacion_actual' => 'required|max:250',
            'tipo_cancer' => 'required',
            'estado_actual_cancer' => 'nullable|max:100',
            'fecha_desde' => 'required|date_format:Y-m-d',
            'relacion_representante' => 'required|numeric'
        ]);
        
        $nino = new Nino;
        $nino->nombre = $request['nombre'];
        $nino->apellido = $request['apellido'];
        $nino->genero = $request['genero']==1?'M':'F';
        $nino->fecha_nacimiento = $request['fecha_nacimiento'];
        $nino->situacion_actual = $request['situacion_actual'];
        $nino->relacion_repr = Nino::$relacionesRepr[(int)$request['relacion_representante']];
        if ($request->has('identificacion'))
        {
            $nino->identificacion = $request['identificacion'];
        }
        $nino->representante_id = Auth::user()->rol_id;

        $nino->save();
        
        $bitacora = new Bitacora;
        $bitacora->accion = "insertar";
        $bitacora->tabla = "nino";
        $bitacora->usuario_id = Auth::user()->id;
        $bitacora->fecha = Carbon::now('America/Caracas');
        $bitacora->save();

        $nino_cancer = new Nino_Cancer;
        $nino_cancer->id = Nino_Cancer::getNextId();
        $nino_cancer->fecha_desde = $request['fecha_desde'];
        if($request->has('estado_actual_cancer'))
        {
            $nino_cancer->estado_actual = $request['estado_actual_cancer'];
        }
        if($request->has('otro_cancer'))
        {
            $nino_cancer->nombre_otro = $request['otro_cancer'];
        }
        $nino_cancer->nino_id = $nino->id;
        $nino_cancer->cancer_id = $request['tipo_cancer'];

        $nino_cancer->save();
        
        $bitacora = new Bitacora;
        $bitacora->accion = "insertar";
        $bitacora->tabla = "nino_cancer";
        $bitacora->usuario_id = Auth::user()->id;
        $bitacora->fecha = Carbon::now('America/Caracas');
        $bitacora->save();

        return redirect()->route('inicio');
    }

    public function infoNino($id)
    {
        $nino = Nino::with([
            'medicamentos' => function($query){
                $query->where('estado_requerimiento', 'Requerido')->where('cantidad', '>', 0)->where('fecha', '>=', Carbon::now('America/Caracas'));
            },
            'insumos' => function($query){
                $query->where('estado_requerimiento', 'Requerido')->where('cantidad', '>', 0)->where('fecha', '>=', Carbon::now('America/Caracas'));
            }])->find($id);
        if(!$nino){
            return redirect()->back();
        }

        return view('informacion_nino', ['nino' => $nino]);
    }
    
    public function modificacionNino($nino_id)
    {
        $nino = Nino::find($nino_id);
        if(!$nino){
            return redirect()->route('inicio');
        }
        $canceres = Cancer::orderBy('nombre')->get();
        return view('modificacion_nino', ['nino' => $nino, 'relacionesRepr' => Nino::$relacionesRepr, 'canceres' => $canceres]);
    }

    public function modificarNino($nino_id, Request $request)
    {
        $nino = Nino::find($nino_id);
        if(!$nino){
            return redirect()->route('inicio');
        }

        $this->validate($request, [
            'nombre' => 'required',
            'apellido' => 'required',
            'fecha_nacimiento' => 'required|date_format:Y-m-d',
            'genero' => 'required',
            'identificacion' => 'nullable',
            'situacion_actual' => 'required|max:250'
        ]);
        
        $nino->nombre = $request['nombre'];
        $nino->apellido = $request['apellido'];
        $nino->fecha_nacimiento = $request['fecha_nacimiento'];
        $nino->genero = $request['genero']==1?'M':'F';
        $nino->relacion_repr = Nino::$relacionesRepr[(int)$request['relacion_representante']];
        if ($request->has('identificacion'))
        {
            $nino->identificacion = $request['identificacion'];
        }
        $nino->situacion_actual = $request['situacion_actual'];
        $nino->save();
        
        $bitacora = new Bitacora;
        $bitacora->accion = "actualizar";
        $bitacora->tabla = "nino";
        $bitacora->usuario_id = Auth::user()->id;
        $bitacora->fecha = Carbon::now('America/Caracas');
        $bitacora->save();

        return redirect()->back();
    }

    public function agregarCancer($nino_id, Request $request) 
    {
        $nino_cancer = new Nino_Cancer;
        $nino_cancer->id = Nino_Cancer::getNextId();
        $nino_cancer->fecha_desde = $request['fecha'];
        if($request->has('estado_actual'))
        {
            $nino_cancer->estado_actual = $request['estado_actual'];
        }
        if($request->has('otro_nombre'))
        {
            $nino_cancer->nombre_otro = $request['otro_nombre'];
        }
        $nino_cancer->nino_id = $nino_id;
        $nino_cancer->cancer_id = $request['cancer'];

        $nino_cancer->save();
        
        $bitacora = new Bitacora;
        $bitacora->accion = "insertar";
        $bitacora->tabla = "nino_cancer";
        $bitacora->usuario_id = Auth::user()->id;
        $bitacora->fecha = Carbon::now('America/Caracas');
        $bitacora->save();

        return redirect()->back(); 
    }

    public function modificarCancer($nino_id, $id, $cancer_id, Request $request) 
    {
        $insert = [
            'fecha_desde' => $request['fecha'],
            'cancer_id' => $request['cancer_id']
        ];
        if($request->has('estado_actual'))
            $insert['estado_actual'] = $request['estado_actual'];
        if($request->has('otro_nombre'))
            $insert['nombre_otro'] = $request['otro_nombre'];
        Nino_Cancer::where('nino_id', $nino_id)
                    ->where('cancer_id', $cancer_id)
                    ->where('id', $id)
                    ->update($insert);
        
        $bitacora = new Bitacora;
        $bitacora->accion = "actualizar";
        $bitacora->tabla = "nino_cancer";
        $bitacora->usuario_id = Auth::user()->id;
        $bitacora->fecha = Carbon::now('America/Caracas');
        $bitacora->save();

        return redirect()->back();
    }

    public function eliminarCancer($nino_id, $id, $cancer_id)
    {
        Nino_Cancer::where('nino_id', $nino_id)
                        ->where('cancer_id', $cancer_id)
                        ->where('id', $id)
                        ->delete();
        
        $bitacora = new Bitacora;
        $bitacora->accion = "eliminar";
        $bitacora->tabla = "nino_cancer";
        $bitacora->usuario_id = Auth::user()->id;
        $bitacora->fecha = Carbon::now('America/Caracas');
        $bitacora->save();

        return redirect()->back();
    }

    public function eliminarNino(Request $request)
    {   
        if(!Nino::find($request['nino_id']))
            return redirect()->route('inicio');
        Nino::where('id', $request['nino_id'])->delete();
        
        $bitacora = new Bitacora;
        $bitacora->accion = "eliminar";
        $bitacora->tabla = "nino";
        $bitacora->usuario_id = Auth::user()->id;
        $bitacora->fecha = Carbon::now('America/Caracas');
        $bitacora->save();

        return redirect()->route('ver-perfil');
    }
}
