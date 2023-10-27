import React, { Component,useEffect,useState  } from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';
import { ToastContainer } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
import { Formik  } from 'formik';
import { Form,Button,Alert,Dropdown } from 'react-bootstrap'
import {Select} from '../includes/Inputs'
import ManualDataConfig from './ManualDataConfig'
import ManualDataFieldRename from './ManualDataFieldRename';
import ManualDataFieldValues from './ManualDataFieldValues';
import { isEmpty, isNull } from 'lodash';

const ManualData = (props) => {

    const [tables,setTables] = useState([])
    const [fields,setFields] = useState([])
    const [error,setError] = useState('')
    const [warning,setWarning] = useState('')
    

    const [config,setConfig] = useState({
        show:false,
        data: {}
    })

    const [fieldRename,setFieldRename] = useState({
        show:false,
        data: {}
    })

    const [fieldValues,setFieldValues] = useState({
        show:false,
        data: {},
        type:''
    })

    const [enterprices, setEnterprices] = useState([]);

    const [enterpriceId,setEnterpriceId] = useState(0)
    const [backUrl,setBackUrl] = useState('')

    const [loading, setLoading] = useState(false)
    const [enterpriceUrl, setEnterpriceUrl] = useState('')
    
    /* functions */
    
  

    function getCurrentEnterprice(){
        const urlParams = new URLSearchParams(window.location.search)
        const enterpriceId =  urlParams.get('emp_id')
        
        if(enterpriceId) {
            setEnterpriceId(enterpriceId)
            setBackUrl(props.baseUrl + '/empresa/'+enterpriceId)
        }
        else{
            setEnterpriceId(0)
            setBackUrl(props.backUrl)
        } 
            
        return enterpriceId
    }
    
    return (
        <div className="banner col-md-12 mb-4 mr-3" >
            <h4>
               <i className="fa fa-database"></i>
               Carga de datos manuales
            </h4>
            <hr></hr>
            <div className="row mb-3">
                    <div className="col-sm-6">
                       
                    </div>
                    {
                        props.backUrl && !props.enterpriseId &&
                        (<div className="col-sm-6">
                            <a className="btn btn-primary text-white float-right" href={props.backUrl} >
                                <i className="fa fa-undo"></i>
                                Regresar
                            </a>
                        </div>)
                    }
                    
            </div>
            <div>
            <Formik
                initialValues={{
                    enterprise_id: '',
                    table: '',
                }}
                enableReinitialize 
           
                onSubmit={async values =>  {   
                    
                    
                }}>
                {
                    ({
                        handleSubmit,
                        handleChange,
                        setTouched,
                        handleBlur,
                        resetForm,
                        setValues,
                        setFieldValue,
                        setFieldTouched,
                        values,
                        touched,
                        isValid,
                        errors,
                        nextState 
                    })=>{
                        
                        useEffect( () => {
                            //const enterpriceId = getCurrentEnterprice()
                            resetForm()
                            fetchEnterprices()
                            console.log(props.enterpriseId)
                            if(props.enterpriseId)
                            {
                                setValues({
                                    enterprise_id: props.enterpriseId
                                })
                                //fetchTables(props.enterpriseId)
                            }
                            
                        }, [props.dataUserLevel]);

                        useEffect( () => {
                            if(values.enterprise_id && values.enterprise_id.toString().length > 0) 
                            {
                                setFieldValue('table','')
                                fetchTables(values.enterprise_id)
                            }else{
                                setFieldValue('table','')
                                setTables([])
                            }
                        }, [values.enterprise_id]);

                        useEffect( () => {
                            if(values.enterprise_id && values.table && values.enterprise_id.toString().length > 0 && values.table.toString().length > 0) 
                                fetchFields(values.table)
                            else
                                setFields([])
                        }, [values.table]);

                        async function fetchEnterprices(){
                            const res = await axios.get(props.baseUrl + '/api/enterprices',{})
                            
                            if(res.statusText == "OK" || res.status == 200){
                                const data = res.data ? res.data : []
                                
                                setError('')
                                setEnterprices(data) 
                                if(data.length == 1) {
                                    setValues({
                                        enterprise_id: data[0].id
                                    })
                                }
                            }else{
                                setError(res.data)
                                setEnterprices([]) 
                            }
                            
                        }
                        
                        async function fetchTables(id){
                            const res = await axios.get(props.baseUrl + `/api/manual/config/${id}/tables`,{})
            
                            
                            switch (res.status) {
                                case 200:
                                    const data = res.data ? res.data : []
                                    setError('')
                                    setWarning('')
                                    setTables(data)
                                    setFields([])
                                    if(data.length == 1)
                                    {
                                        setFieldValue('table',data[0].name)
                                    }   
                                    break;
                                case 449:
                                    setError('')
                                    setWarning(res.data)
                                    setTables([])
                                    setFields([])
                                    break;
                                default:
                                    setError(res.data)
                                    setWarning('')
                                    setTables([])
                                    setFields([])
                                    break;
                            }
                        }

                        async function openConfig(){
                            const res = await axios.get(props.baseUrl + `/api/manual/config/${values.enterprise_id}`,{})
                            const data = res.data ? res.data : {}
                            setConfig({
                                show:true,
                                data: data
                            })
                        }

                        async function fetchFields(table){
                            const res = await axios.get(props.baseUrl + `/api/manual/fields/${values.enterprise_id}/${table}`,{})
                            if(res.statusText == "OK" || res.status == 200){
                                const data = res.data ? res.data : []
                                setError('')
                                setFields(data)    
                            }else{
                                setError(res.data)
                            }
                        }

                        async function onHideRename(resp){
                            setFieldRename({
                                show:false,
                                data:{}
                            })
                            if(resp) fetchFields(values.table)
                        }

                        async function onHideValue(resp){
                            setFieldValues({
                                show:false,
                                data:{},
                                type: ''
                            })
                        }

                        return (
                            <Form noValidate  onSubmit={handleSubmit}>
                                
                                <Form.Group>
                                        <Form.Row>
                                            <Select disabled={props.enterpriseId} className="col-6" name="enterprise_id" value={values.enterprise_id} label="Empresa" placeholder="Seleccione la empresa" onChange={handleChange} >
                                                {
                                                    enterprices && enterprices.map(
                                                        (en)=>
                                                            <option key={en.id} value={en.id}>{en.name}</option>
                                                    )
                                                }
                                            </Select>
                                            {
                                                props.dataUserLevel == 1 &&
                                                <div className="col-2 pt-3" style={{"display": "flex"}}>
                                                    <Button variant="default" size="sm" style={{"margin": "auto"}} onClick={openConfig}>
                                                        <i className="fa fa-cogs"></i>
                                                        Acceso
                                                    </Button>
                                                </div>
                                            }
                                            <div className={props.dataUserLevel == 1 ? 'col-4': 'col-6'}>
                                                <Select  name="table" value={values.table} label="Tabla" placeholder="Seleccione la tabla" onChange={handleChange} >
                                                    {
                                                        tables && tables.map(
                                                            (t)=>
                                                                <option key={t.name} value={t.name}>{t.name}</option>
                                                        )
                                                    }
                                                </Select>
                                            </div>
                                            
                                        </Form.Row>  
                                        <Form.Row>
                                            <div className="col-12">
                                            {
                                                error && 
                                                (
                                                    <Alert  variant="danger">
                                                        Ha ocurrido un error con la configuración de datos locales para la empresa seleccionada
                                                        {error}
                                                    </Alert>
                                                ),
                                                warning &&
                                                (
                                                    <Alert variant="warning">
                                                        No se ha podido cargar la configuración para la empresa seleccionada.<br/>
                                                        {warning}
                                                    </Alert>
                                                )
                                            }
                                            </div>
                                        </Form.Row>
                                        <Form.Row>
                                            {
                                                fields && fields.map(
                                                    (f)=>
                                                    <Dropdown  size="lg" className="col-3"  key={'f'+f.name}>
                                                                <Dropdown.Toggle variant="light" id={'f'+f.name} style={
                                                                    {
                                                                        width: "100%",
                                                                        height: "80px",
                                                                        fontSize: "20px",
                                                                        margin: "2px"
                                                                    }
                                                                }>
                                                                    {f.name}
                                                                    <i className="fa fa-options"></i>
                                                                </Dropdown.Toggle>

                                                                <Dropdown.Menu >
                                                                    
                                                                    <Dropdown.Item style={{padding: "6px",fontSize: "14px"}} 
                                                                    onClick={(index)=>{
                                                                        setFieldRename(
                                                                            {
                                                                                show:true,
                                                                                data:{
                                                                                    enterpriseId: values.enterprise_id,
                                                                                    field: f.name,
                                                                                    table: values.table
                                                                                }
                                                                            }
                                                                        )
                                                                    }}>
                                                                        <i className="fa fa-edit"></i>
                                                                        Cambiar nombre
                                                                    </Dropdown.Item>
                                                                    
                                                                    <Dropdown.Item style={{padding: "6px",fontSize: "14px"}}
                                                                    onClick={()=>{
                                                                        setFieldValues({
                                                                            show:true,
                                                                            type:'day',
                                                                            data:{
                                                                                enterpriseId: values.enterprise_id,
                                                                                field: f.name,
                                                                                table: values.table
                                                                            }
                                                                        })
                                                                    }}>
                                                                        <i className="fa fa-calendar"></i>
                                                                        Entrar valores diarios
                                                                    </Dropdown.Item>

                                                                    <Dropdown.Item style={{padding: "6px",fontSize: "14px"}}
                                                                    onClick={()=>{
                                                                        setFieldValues({
                                                                            show:true,
                                                                            type:'month',
                                                                            data:{
                                                                                enterpriseId: values.enterprise_id,
                                                                                field: f.name,
                                                                                table: values.table
                                                                            }
                                                                        })
                                                                    }}>
                                                                        <i className="fa fa-calendar"></i>
                                                                        Entrar valores mensuales
                                                                    </Dropdown.Item>

                                                                    <Dropdown.Item style={{padding: "6px",fontSize: "14px"}}
                                                                    onClick={()=>{
                                                                        setFieldValues({
                                                                            show:true,
                                                                            type:'year',
                                                                            data:{
                                                                                enterpriseId: values.enterprise_id,
                                                                                field: f.name,
                                                                                table: values.table
                                                                            }
                                                                        })
                                                                    }}>
                                                                        <i className="fa fa-calendar"></i>
                                                                        Entrar valores anuales
                                                                    </Dropdown.Item>
                                                                    
                                                                </Dropdown.Menu>
                                                            </Dropdown>
                                                )
                                            }
                                        </Form.Row>
                                           
                                </Form.Group>
                                
                                <ManualDataConfig show={config.show} enterpriseId={values.enterprise_id} data={config.data} onHide={()=>{setConfig({show:false,data:config.data})}} />
                                <ManualDataFieldRename show={fieldRename.show} data={fieldRename.data} onHide={onHideRename} />
                                <ManualDataFieldValues show={fieldValues.show} data={fieldValues.data} type={fieldValues.type} onHide={onHideValue} />
                            </Form>
                        
                        )
                    }
                }

            </Formik>
            </div>
          
            <ToastContainer
                    position="top-right"
                    autoClose={5000}
                    hideProgressBar={false}
                    newestOnTop={false}
                    closeOnClick
                    rtl={false}
                    pauseOnFocusLoss
                    draggable
                    pauseOnHover
                    />
                
            <ToastContainer />
        </div>
    )
}



if (document.querySelectorAll('[data-manual]').length > 0) {
    const docs = document.querySelectorAll('[data-manual]')
    docs.forEach(doc => {
        ReactDOM.render(<ManualData
            enterpriseId={doc.getAttribute('data-enterprise-id')} 
            baseUrl={doc.getAttribute('data-base-url')} 
            backUrl={doc.getAttribute('data-back-url')} 
            dataUserLevel={doc.getAttribute('data-user-level')} 
            />, doc); 
    });
}

