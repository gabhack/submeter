import React, { useEffect,useState   } from 'react';
import { toast} from 'react-toastify';
import { Modal,Button,Form,Spinner } from 'react-bootstrap'
import * as Yup from 'yup'
import { Formik  } from 'formik';
import {InputText} from '../includes/Inputs'
import Loading from '../includes/Loading'

const ManualDataFieldRename = (props) => {
    const [loading,setLoading] = useState(false)
    return (
        <div>
        
            <Formik
                initialValues={{
                    name: ''
                }}
                enableReinitialize={true}
            
                validationSchema={
                    Yup.object().shape({
                        name:Yup.string().required('Este campo es obligatorio')
                    })
                }
                
                onSubmit={async values  => {  
                    setLoading(true)
                    const resp = await axios.post(`/api/manual/fields/${props.data.enterpriseId}/${props.data.table}/${props.data.field}`,values)
                    if(resp.statusText == "OK")
                    {
                        toast.success('El nombre se ha cambiado correctamente')
                        props.onHide(values)     
                    }else{
                        toast.error(resp.data)
                    }
                    setLoading(false)
                }}>
                    {
                        ({
                            handleSubmit,
                            handleChange,
                            resetForm,
                            setFieldValue,
                            values
                        })=>{
                        
                            useEffect(() => {
                                resetForm()
                                setFieldValue('name','')
                            }, [props.show]);

                        return (
                            <Modal show={props.show} size="sm" aria-labelledby="contained-modal-title-vcenter" onHide={props.onHide} className="fade">
                                <Modal.Header closeButton>
                                    <Modal.Title id="contained-modal-title-vcenter">
                                        <i className="fa fa-edit"></i>
                                        Cambiar nombre de campo
                                    </Modal.Title>
                                </Modal.Header>
                                
                                <Modal.Body  >
                                    <Form noValidate  onSubmit={handleSubmit} >
                                        <Loading show={loading}/>
                                        <InputText className="col-12" type="text" name="name" value={values.name} label="Nuevo nombre" placeholder="Entre el nuevo nombre del campo" onChange={handleChange} />                                        
                                    </Form>
                                </Modal.Body>
                                <Modal.Footer>
                                    <Button variant="default"  onClick={props.onHide }>
                                        <i className="fa fa-times"></i>
                                        Cerrar
                                    </Button>
                                    <Button variant="primary" onClick={handleSubmit} disabled={loading} >
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

export default ManualDataFieldRename;