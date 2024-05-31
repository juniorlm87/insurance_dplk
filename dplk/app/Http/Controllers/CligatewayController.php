<?php

namespace App\Http\Controllers;

use App\Models\Cligateway;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use PDO;

class CligatewayController extends Controller
{
    
    protected $user;
 
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        /*
        return $this->user
            ->cligateways()
            ->get();
            */

            return response()->json([
                'success' => true,
                'message' => 'Success',
                'data' => $request->all()
            ], Response::HTTP_OK);
    }

    public function action(Request $request,$route_name){
        
        //$route_name
        if(!preg_match('/^[a-zA-Z\_\.\-]+$/i',$route_name)){
            return response()->json(['error' =>'Invalid route name'], 200);
        }
        
        
        
        $result = DB::table('cligateways')
        ->where('route_name','=',$route_name)
        ->where('access_status','=',1)
        ->get()->first();
        
        if($result == null){
            return response()->json(['error' =>'Route name not registered'], 200);
        }else{

            $parser=json_decode($result->parameter,true);
            
            


            $result=array();
            $alert=array();
            $param_arr=array();
            $no=0;
            $db =null;
            $query=null;
            $rows=null;
            $stmt=null;
            $param_url_arr=array();
            foreach($parser as $rs){
                
                foreach($rs['parameter'] as $ps){
                    $pdata=request($ps['param_name']);
                    
                    if($pdata=="" or strlen($pdata)<=2){
                        $alert[$no][]=$ps['param_name']." is invalid";
                        continue;
                    }else{
                        $param_arr[$no][]=$pdata;
                        $param_url_arr[$no][$ps['param_name']]=$pdata;
                    }
                }
                
                $param_arr[$no]=isset($param_arr[$no]) ? $param_arr[$no]:false;

                if($param_arr[$no]==false){
                    return response()->json(['error' =>'No parameter found'], 200);
                }
                
                if($rs['pdo_driver']=="mysql"){
                    try{
                        $db = new PDO($rs['pdo_driver'].":host=".$rs['pdo_srv'].";port=".$rs['pdo_srv_port'].";dbname=".$rs['pdo_db'],$rs['pdo_username'],$rs['pdo_password']);
                        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        $stmt = $db->prepare($rs['query']);
                        $stmt->execute($param_arr[$no]);
                        $rows=$stmt->fetchAll(PDO::FETCH_ASSOC);
                        $result[$rs['pdo_obj_name']]=$rows;

                    }catch(PDOException $er){
                        
                        $result[$rs['pdo_obj_name']]=$rs['pdo_obj_name'].'='.$er->getMessage();
                        
                    }
                }elseif($rs['pdo_driver']=="dblib"){
                    try{
                        $db = new PDO($rs['pdo_driver'].":host=".$rs['pdo_srv'].";port=".$rs['pdo_srv_port'].";version=7.0;charset=UTF-8;dbname=".$rs['pdo_db'],$rs['pdo_username'],$rs['pdo_password']);
                        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        $stmt = $db->prepare($rs['query']);
                        $stmt->execute($param_arr[$no]);
                        $rows=$stmt->fetchAll(PDO::FETCH_ASSOC);
                        $result[$rs['pdo_obj_name']]=$rows;	
                    }catch(PDOException $er){
                        
                        $result[$rs['pdo_obj_name']]=$rs['pdo_obj_name'].'='.$er->getMessage();
                    }
                    
                }elseif($rs['pdo_driver']=="curl"){
                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                    CURLOPT_URL => $rs['query'],
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => http_build_query($param_url_arr[$no]),
                    CURLOPT_HTTPHEADER => array(),
                    ));

                    $response = curl_exec($curl);
                    $result[$rs['pdo_obj_name']]=$response;	
                    curl_close($curl);
                    $curl =null;
                    $response =null;
                }
                
                $db =null;
                $query=null;
                $rows=null;
                $stmt=null;
                $no++;
            }
            
            if(count($alert)>0){
                return response()->json([
                    "error" => "Invalid parameter",
                    "data"=>$alert
                ], Response::HTTP_OK);
                
            }else{
                return response()->json([
                    "message"=>"Success",
                    "data"=>$result
                ], Response::HTTP_OK);
               
            }

        }

    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Cligateway  $cligateway
     * @return \Illuminate\Http\Response
     */
    public function show(Cligateway $cligateway)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Cligateway  $cligateway
     * @return \Illuminate\Http\Response
     */
    public function edit(Cligateway $cligateway)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cligateway  $cligateway
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cligateway $cligateway)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Cligateway  $cligateway
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cligateway $cligateway)
    {
        //
    }
}
