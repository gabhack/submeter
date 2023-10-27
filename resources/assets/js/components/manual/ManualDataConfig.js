import React, { useEffect  } from 'react';
import { Formik } from 'formik';
import * as Yup from 'yup'
import { Modal,Button,Form } from 'react-bootstrap'
import {InputText} from '../includes/Inputs'
import { toast,ToastContainer } from 'react-toastify';


const ManualDataConfig = (props) => {
    
    return (
        <Formik
            initialValues={{
                hostname: '',
                database: '',
                port: '',
                username: '',
                password: '',
            }}
            enableReinitialize 
           
            validationSchema={
                Yup.object().shape({
                    hostname: Yup.string().required('Este campo es obligatorio'),
                    database: Yup.string().required('Este campo es obligatorio'),
                    port: Yup.string().required('Este campo es obligatorio'),
                    username: Yup.string().required('Este campo es obligatorio'),
                    password: Yup.string().required('Este campo es obligatorio'),
                })
            }
            
            onSubmit={async (values)  => {  
                const resp = await axios.post(`/api/manual/config/${props.enterpriseId}`,values)
                if(resp.statusText == "OK")
                {
                    toast.success('La configuracion de acceso se ha definido correctamente')
                    props.onHide()     
                }else{
                    toast.error(resp.data)
                }
                
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
                            const data  = props.data ? props.data : {}; 
                            setValues( {
                                hostname: data.hostname ? data.hostname : '',
                                database: data.database ? data.database : '',
                                port: data.port ? data.port : '',
                                username: data.username ? data.username : '',
                                password: data.password ? data.password : '',
                            })
                        }, [props.show]);

                        return (
                        <Modal show={props.show} size="md" onHide={props.onHide} className="fade ">
                            <Modal.Header closeButton>
                                <Modal.Title id="contained-modal-title-vcenter">
                                    <h3>
                                        <i className="fa fa-database"></i>
                                        Definir acceso a base de datos
                                    </h3>
                                </Modal.Title>
                            </Modal.Header>
                            
                            <Modal.Body  >
                                <Form noValidate  onSubmit={handleSubmit}>
                                    <Form.Row>    
                                        <InputText className="col-12" type="text" name="hostname" value={values.hostname} label="Host" placeholder="Entre el nombre de host" onChange={handleChange} />
                                    </Form.Row>  
                                    <Form.Row>
                                        <InputText className="col-8" type="text" name="database" value={values.database} label="Base de datos" placeholder="Entre el nombre de la base de datos" onChange={handleChange} />
                                        <InputText className="col-4" type="number" name="port"  value={values.port} label="Puerto" placeholder="Entre el puerto a usar" onChange={handleChange} />
                                    </Form.Row>  
                                    <Form.Row>
                                        <InputText className="col-6" type="text" name="username" value={values.username} label="Usuario" placeholder="Entre el usuario" onChange={handleChange} />
                                        <InputText className="col-6" type="text" name="password"  value={values.password} label="Contraseña" placeholder="Entre la contraseña" onChange={handleChange} />
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
                            
                        </Modal>
                        
                        )
                    }
                }

        </Formik>
    );
};

export default ManualDataConfig;