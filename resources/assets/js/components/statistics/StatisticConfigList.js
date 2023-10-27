import React, { Component,useEffect,useState  } from 'react';
import ReactDOM from 'react-dom';
import Datatable from '../includes/Datatable';
import axios from 'axios';
import { toast,ToastContainer } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
import { confirmation } from '../includes/Confirmation';

const StatisticConfigList = (props) => {
    const [data, setData] = useState([]);
    
    const [loading, setLoading] = useState(false)
    const [enterpriceUrl, setEnterpriceUrl] = useState('')
    
    /* functions */
    function fetchData(row){
        const q = {
            params: {
                type: props.type
            }
        };
        if(props.empId) 
        {
            setEnterpriceUrl('?emp_id='+props.empId)
            q.params.emp_id = props.empId
        }
        axios.get(props.baseUrl + `/api/statics/configs`,q)
        .then(res => {
            setData(res.data)
        },err=>{
            setData([])
        })
    }
  
    useEffect(() => {
        fetchData()
    },[]);

    return (
        <div className="banner col-md-12 mb-4 mr-3" >
            <h4>
                {
                    (props.type == 'indicadores') ? 
                    (<span>Indicadores</span>)
                    :
                    (<span>Producción</span>)
                }
            </h4>
            <hr></hr>
            <div className="row mb-3">
                    <div className="col-sm-6">
                        <a href={props.baseUrl + `/estadisticas/configuracion/${props.type}/insertar${enterpriceUrl}`} className="btn btn-success text-white float-left">
                            <i className="fa fa-plus"></i>
                            Nueva configuración
                        </a>
                    </div>
                    {
                        props.backUrl &&
                        (<div className="col-sm-6">
                            <a className="btn btn-primary text-white float-right" href={props.backUrl} >
                                <i className="fa fa-undo"></i>
                                Regresar
                            </a>
                        </div>)
                    }
                    
            </div>
            <div>
                <Datatable data={data} className="table table-striped table-responsive bg-white mt-3" loading={loading} 
                columns={
                    [
                        { 
                            title: "Nombre", 
                            data:"name" 
                        },
                        {
                            title: "Editar", 
                            data: null,
                            searchable: false,
                            sortable: false,
                            width: 50,
                            action:{
                                className:'btn btn-primary',
                                icon:'fa fa-pen',
                                event:'update'
                            }
                        },
                        {
                            title: "Remover",
                            data:null, 
                            searchable: false,
                            sortable: false,
                            width: 50,
                            action:{
                                className:'btn btn-danger btn-delete-assign-energy',
                                icon:'fa fa-times',
                                event:'remove'
                            }
                        }
                    ]
                }

                onUpdate={(row)=>{
                    window.location.href = props.baseUrl + `/estadisticas/configuracion/${props.type}/modificar/${row.id}${enterpriceUrl}`
                }}

                onRemove={(row)=>{
                    confirmation({
                        header:'Eliminar configuración',
                        body:'Seguro que desea eliminar la configuración',
                        onConfirm:()=>{
                            axios.post(props.baseUrl + `/api/statics/configs/${row.id}`,{
                                _method:'DELETE'
                            })
                            .then(res => {
                                toast.success("La configuración se ha eliminado correctamente")
                                fetchData()
                            })
                        }
                    })
                }}
                >
                <thead className="bg-submeter-4">
                        <tr>
                            <th className="text-white"  width="80%">Nombre</th>
                            <th className="text-white" >Editar</th>
                            <th className="text-white" >Eliminar</th>
                        </tr>
                </thead>
                <tbody></tbody>
            </Datatable> 
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



if (document.querySelectorAll('[data-statistic-config-list]').length > 0) {
    const docs = document.querySelectorAll('[data-statistic-config-list]')
    docs.forEach(doc => {
        ReactDOM.render(<StatisticConfigList
            type={doc.getAttribute('data-statistic-config-list')}
            baseUrl={doc.getAttribute('data-base-url')} 
            backUrl={doc.getAttribute('data-back-url')} 
            empId={doc.getAttribute('data-emp-id')}
            />, doc); 
    });
}

