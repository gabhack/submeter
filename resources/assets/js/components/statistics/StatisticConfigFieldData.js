import React, { Component,useEffect,useState   } from 'react';

import { Modal,Button,Form,Col,Table,Row,Card } from 'react-bootstrap'


import * as Yup from 'yup'
import { Formik  } from 'formik';
import {Select} from '../includes/Inputs'
import Loading from '../includes/Loading'


const StatisticConfigFieldData = (props) => {
    const data  = props.data ? props.data : {}; 

    const [hostnames,setHostnames] = useState([])
    const [databases,setDatabases] = useState([])
    const [tables,setTables] = useState([])
    const [fields,setFields] = useState([])

    function loadDatabases(){
        const dbs = [];
        if(props.metadata != null)
        {
            props.metadata.map(f=>{
                dbs.push({
                    id:f.id,
                    name:f.name
                })
            })
        }
        setDatabases(dbs)
    }

    function loadTables(id){
        
        const tables = [];
        if(props.metadata != null && id)
        {
            const db = props.metadata.find(f=>f.id == id)
            db.tables.map(f=>{
                tables.push({
                    id:f.id,
                    name:f.name
                })
            })
        }
        setTables(tables)
    }

    function loadFields(database,table){
        const fields = [];
        if(props.metadata != null && database && table)
        {
            const db = props.metadata.find(f=>f.id == database)
            const tb = db.tables.find(f=>f.name == table)
            tb.fields.map(f=>{
                fields.push({
                    id:f.id,
                    name:f.name
                })
            })
        }
        setFields(fields)
    }

    return (
        <div>
        
            <Formik
                initialValues={{
                    id: 0,
                    connection:'',
                    table:'',
                    field:'',
                    group_by:'avg',
                    key: ''
                }}
                enableReinitialize={true}
            
                validationSchema={
                    Yup.object().shape({
                        connection:Yup.string().required('Este campo es obligatorio'),
                        table:Yup.string().required('Es obligatorio'),
                        field:Yup.string().required('Este campo es obligatorio'),
                        group_by:Yup.string().required('Este campo es obligatorio')
                    })
                }
                
                onSubmit={values => {  
                    const connName = props.metadata.find(f=>f.id == values.connection).name
                    values.key = `${values.group_by}(${connName}.${values.table}.${values.field})`
                    props.onHide(values)
                }}>
                    {
                        ({
                            handleSubmit,
                            handleChange,
                            handleBlur,
                            resetForm,
                            validateForm,
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
                                loadDatabases()
                                setValues( {
                                    id: data.id ? data.id : 0,
                                    connection:data.connection ? data.connection: '',
                                    table:data.table ? data.table : '',
                                    field:data.field ? data.field : '',
                                    group_by:data.group_by ? data.group_by : 'avg'
                                })
                            }, [props.show]);

                        return (
                            <Modal show={props.show} size="sm" aria-labelledby="contained-modal-title-vcenter" onHide={props.onHide} className="fade">
                                <Modal.Header closeButton>
                                    <Modal.Title id="contained-modal-title-vcenter">
                                        {(values.id == 0) ? 
                                        (
                                        <h5>
                                            <i className="fa fa-plus"></i>
                                            Insertar campo de base de datos
                                        </h5>
                                        ):
                                        (
                                        <h5>
                                            <i className="fa fa-edit"></i>
                                            Editar campo de base de datos
                                        </h5>
                                        )   
                                        }
                                    </Modal.Title>
                                </Modal.Header>
                                
                                <Modal.Body  >
                                    <Loading show={props.loading}/>
                                    <Form noValidate  onSubmit={handleSubmit} >
                                        
                                        <Select name="connection" value={values.connection} label="Conección" placeholder="Seleccione la connección base de datos" onChange={(e)=>{handleChange(e);loadTables(e.target.value)}} >
                                            {
                                                databases.length > 0 && databases.map(
                                                    (f)=><option key={f.id} value={f.id}>{f.name}</option>
                                                )
                                            }
                                        </Select>
                                        <Select name="table" value={values.table} label="Tabla" placeholder="Seleccione la tabla" onChange={(e)=>{handleChange(e);loadFields(values.connection,e.target.value)}} >
                                            {
                                                tables.length > 0 && tables.map(
                                                    (f)=><option key={f.name} value={f.name}>{f.name}</option>
                                                )
                                            }
                                        </Select>

                                        <Select name="field" value={values.field} label="Campo" placeholder="Seleccione el campo" onChange={handleChange} >
                                            {
                                                fields.length > 0 && fields.map(
                                                    (f)=><option key={f.name} value={f.name}>{f.name}</option>
                                                )
                                            }
                                        </Select>

                                        <Select name="group_by" value={values.group_by} label="Agrupar por intervalos en" placeholder="Seleccione método de agrupamiento" onChange={handleChange} >
                                            <option value="avg">Promedio</option>
                                            <option value="max">Mayor</option>
                                            <option value="min">Menor</option>
                                            <option value="rep">Representación</option>
                                        </Select>

                                    </Form>
                                </Modal.Body>
                                <Modal.Footer>
                                    <Button variant="default"  onClick={props.onHide }>
                                        <i className="fa fa-times"></i>
                                        Cerrar
                                    </Button>
                                    <Button variant="primary" onClick={handleSubmit}  >
                                        <i className="fa fa-check-square"></i>
                                        Aceptar
                                    </Button>
                                </Modal.Footer>
                                
                            </Modal>
                            )
                        }
                    }
                    
            </Formik>
        </div>
    );
};

export default StatisticConfigFieldData;