import React, { useEffect,useState  } from 'react';
import { Formik } from 'formik';
import * as Yup from 'yup'
import { Modal,Button,Form,Col,Table,Row,Card,Dropdown } from 'react-bootstrap'
import {InputText, Select,InputColor, InputArea,InputMultiselect} from '../includes/Inputs'
import StatisticConfigFieldData from './StatisticConfigFieldData'


const StatisticConfigField = (props) => {
    const data  = props.data ? props.data : {}; 

    const [currentFieldData, setCurrentFieldData] = useState({
        show : false,
        data: {},
        metadata: []
    })
    
    function insertFieldData(){
        setCurrentFieldData({
            show:true,
            data: {
                id: 0,
                host:'',
                database:'',
                table:'',
                field:'',
                group_by:''
            }
        })
    }

    
    return (
        <Formik
            initialValues={{
                id: 0,
                name: '',
                display_name: '',
                field_type: '',
                destiny: [],
                operation_type: '',
                number_type: '',
                unities: '',
                decimals: '',
                color: '',
                database_fields:[],
                expression: ''
            }}
            enableReinitialize 
           
            validationSchema={
                Yup.object().shape({
                    name: Yup.string().required('Este campo es obligatorio'),
                    display_name: Yup.string().required('Este campo es obligatorio'),
                    field_type: Yup.string().required('Este campo es obligatorio'),
                    destiny: Yup.array().required('Este campo es obligatorio'),
                    operation_type: Yup.string().required('Este campo es obligatorio'),
                    number_type: Yup.string().required('Este campo es obligatorio'),
                    unities: Yup.string().required('Este campo es obligatorio'),
                    decimals: Yup.string().required('Este campo es obligatorio'),
                    color: Yup.string().required('Este campo es obligatorio'),
                    expression: Yup.string().required('Este campo es obligatorio')  
                })
            }
            
            onSubmit={values => {   
                values.field_type_name = ''
                let t = props.productionTypes.find(f=>f.id == values.field_type)
                if(t) values.field_type_name = t.name
                props.onSave(values)
            }}>
                {
                    ({
                        handleSubmit,
                        handleChange,
                        handleBlur,
                        resetForm,
                        setValues,
                        setFieldValue,
                        values,
                        touched,
                        isValid,
                        errors,
                        nextState 
                    })=>{
                        
                        useEffect(() => {
                            resetForm()
                            setValues( {
                                id: data.id ? data.id : 0,
                                name:data.name ? data.name : '',
                                display_name: data.display_name ? data.display_name :'',
                                field_type: data.field_type ? data.field_type :'',
                                destiny: data.destiny ? data.destiny :[],
                                operation_type: data.operation_type ? data.operation_type :'',
                                number_type: data.number_type ? data.number_type :'',
                                unities: data.unities ? data.unities :'',
                                decimals: data.decimals ? data.decimals : (data.number_type && data.number_type == 2 ? '0': ''),
                                color: data.color ? data.color :'',
                                database_fields:data.database_fields ? data.database_fields : [],
                                expression: data.expression ? data.expression :''
                            })

                        }, [props.show]);

                        function onCloseFieldData(data){
                            setCurrentFieldData({show:false})
                            const dbfields = values.database_fields;
                            if(data) 
                            {
                                dbfields.push(data)
                                setFieldValue('database_fields',dbfields)
                            }
                        }
                        
                       return (
                        <Modal show={props.show} size="lg" backdrop="static" aria-labelledby="contained-modal-title-vcenter" onHide={props.onHide} className="fade ">
                        <Modal.Header closeButton>
                            <Modal.Title id="contained-modal-title-vcenter">
                                {(values.id == 0) ? 
                                (
                                <h3>
                                    <i className="fa fa-plus"></i>
                                    Insertar campo
                                </h3>
                                ):
                                (
                                <h3>
                                    <i className="fa fa-write"></i>
                                    Modificar campo
                                </h3>
                                )   
                                }
                            </Modal.Title>
                        </Modal.Header>
                        
                        
                        <Modal.Body  >
                            <Form noValidate  onSubmit={handleSubmit}>
                                <Form.Row>
                                    <InputText className="col-6" type="text" name="name" value={values.name} label="Nombre" placeholder="Entre el nombre" onChange={handleChange} />
                                    <InputText className="col-6" type="text" name="display_name" value={values.display_name} label="Nombre a mostrar" placeholder="Entre el nombre a mostrar" onChange={handleChange} />
                                </Form.Row>
                                <Form.Row>
                                    <Select className="col-6" type="text" name="field_type" value={values.field_type} label="Tipo de campo" placeholder="Seleccione el tipo de campo" onChange={handleChange} >
                                        {
                                            props.productionTypes && props.productionTypes.map(
                                                (t)=>(
                                                    <option key={t.id} value={t.id}>{t.name}</option>
                                                )
                                            ) 
                                        }
                                    </Select>
                                    <InputMultiselect className="col-6" name="destiny" value={values.destiny} label="Mostrar en" 
                                        placeholder="Seleccione donde mostrar" onChange={setFieldValue} 
                                        options={
                                                    [
                                                        { label: "Csv", value: "1" },
                                                        { label: "Gráfica", value: "2" },
                                                        { label: "Totales", value: "3" },
                                                        { label: "Tabla ampliada", value: "4" },
                                                    ]
                                                }
                                    />
                                    
                                </Form.Row>

                                <Form.Row>
                                    <Select className="col-6" type="text" name="operation_type" value={values.operation_type} label="Tipo de operacion" placeholder="Seleccione el tipo de operacion" onChange={handleChange} >
                                        <option value="1" data-min="1" data-max="1">SUMATOTAL</option>
                                        <option value="2" data-min="1" data-max="1">PROMEDIO</option>
                                        <option value="3" data-min="1" data-max="1">MEDIANA</option>
                                        <option value="4" data-min="1" data-max="1"> MIN</option>
                                        <option value="5" data-min="1" data-max="1">MAX</option>
                                        <option value="6" data-min="1" data-max="1"> DESVIACIÓN ESTANDAR</option>   
                                    </Select>
                                    <Select className="col-6" type="text" name="number_type" value={values.number_type} label="Tipo de numero" placeholder="Seleccione el tipo de numero" onChange={(e)=>{
                                        if(e.target.value != 1) setFieldValue('decimals',0)
                                        handleChange(e)}} >
                                        <option value="1">DECIMAL</option>
                                        <option value="2">ENTERO</option> 
                                    </Select>
                                </Form.Row>

                                <Form.Row>
                                    <InputText className="col-5" type="text" name="unities" value={values.unities} label="Unidades" placeholder="Entre el valor de unidades" onChange={handleChange} />
                                    <InputText className="col-5" type="number" name="decimals" readOnly={values.number_type != 1} value={values.decimals} label="Decimales" placeholder="Entre los decimales a usar" onChange={handleChange} />
                                    <InputColor className="col-2" name="color" value={values.color} label="Color"  onChange={handleChange} />
                                </Form.Row>
                                
                                <Form.Row>
                                   <div className="col-6">
                                        <InputArea rows="8" name="expression" value={values.expression} label="Expresión" placeholder="Expresion a evaluar" onChange={handleChange} />
                                   </div>
                                   <div className="col-6">
                                        <div className="row">
                                            <h6 className="col-8">
                                                <i className="fa fa-database"></i>
                                                Campos de base de datos
                                            </h6>
                                            <div className="col-4">
                                                <Button variant="outline-secondary" size="sm"  className="float-right mr-1" onClick={insertFieldData}>
                                                    <i className="fa fa-plus"></i>
                                                    Insertar
                                                </Button>
                                            </div>
                                        </div>
                                        <div>
                                            <Table striped bordered hover size="sm" style={{tableLayout: "fixed"}}>
                                                <tbody>
                                                    {
                                                        values.database_fields.length > 0 && values.database_fields.map((field,index)=>
                                                            <tr key={index}>
                                                                
                                                                <td style={{padding: "0",overflowWrap: "break-word"}}>
                                                                    {field.key}
                                                                </td>
                                                                
                                                                <td style={{padding: "1px 5px",width:"50px"}}>
                                                                    <Dropdown  size="sm" >
                                                                        <Dropdown.Toggle variant="light" id="dropdown-basic">
                                                                            <i className="fa fa-options"></i>
                                                                        </Dropdown.Toggle>

                                                                        <Dropdown.Menu >
                                                                            
                                                                            <Dropdown.Item style={{padding: "6px",fontSize: "14px"}} 
                                                                            onClick={(index)=>{
                                                                                const dbfields = values.database_fields
                                                                                dbfields.splice(index,1)
                                                                                setFieldValue('database_fields',dbfields)
                                                                            }}>
                                                                                <i className="fa fa-trash"></i>
                                                                                Eliminar
                                                                            </Dropdown.Item>
                                                                            
                                                                            <Dropdown.Item style={{padding: "6px",fontSize: "14px"}}
                                                                            onClick={()=>{
                                                                                setFieldValue('expression', ` ${field.key} ` )
                                                                            }}>
                                                                                <i className="fa fa-check"></i>
                                                                                Establecer
                                                                            </Dropdown.Item>

                                                                            <Dropdown.Item style={{padding: "6px",fontSize: "14px"}}
                                                                            onClick={()=>{
                                                                                setFieldValue('expression', `${values.expression} + ${field.key} ` )
                                                                            }}>
                                                                                <i className="fa fa-plus"></i>
                                                                                Sumar
                                                                            </Dropdown.Item>
                                                                            <Dropdown.Item style={{padding: "6px",fontSize: "14px"}}
                                                                            onClick={()=>{
                                                                                setFieldValue('expression', `${values.expression} - ${field.key} ` )
                                                                            }}>
                                                                                <i className="fa fa-minus"></i>
                                                                                Restar
                                                                            </Dropdown.Item>
                                                                            <Dropdown.Item style={{padding: "6px",fontSize: "14px"}}
                                                                            onClick={()=>{
                                                                                setFieldValue('expression', `(${values.expression}) * ${field.key} ` )
                                                                            }}>
                                                                                <i className="fa fa-times"></i>
                                                                                Multiplicar
                                                                            </Dropdown.Item>
                                                                            <Dropdown.Item style={{padding: "6px",fontSize: "14px"}}
                                                                            onClick={()=>{
                                                                                setFieldValue('expression', `(${values.expression}) / ${field.key} ` )
                                                                            }}>
                                                                                <i className="fa fa-divide"></i>
                                                                                Dividir
                                                                            </Dropdown.Item>
                                                                        </Dropdown.Menu>
                                                                    </Dropdown>
                                                                </td>
                                                            </tr>
                                                        )
                                                    }
                                                </tbody>
                                            </Table>
                                        </div>
                                        
                                   </div>
                                </Form.Row>
                                
                            </Form>
                        </Modal.Body>
                        <Modal.Footer>
                            <Button variant="secondary"  onClick={props.onHide }>
                                <i className="fa fa-times"></i>
                                Cerrar
                            </Button>
                            <Button variant="primary" onClick={handleSubmit} >
                                <i className="fa fa-check"></i>
                                Aceptar
                            </Button>
                        </Modal.Footer>
                        <StatisticConfigFieldData onHide={onCloseFieldData} show={currentFieldData.show} data={currentFieldData.data} metadata={props.databaseMeta} />
                        </Modal>
                        
                        )
                    }
                }

        </Formik>
    );
};

export default StatisticConfigField;