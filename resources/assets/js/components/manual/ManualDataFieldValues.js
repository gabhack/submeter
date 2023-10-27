import React, { useEffect,useState   } from 'react';
import { toast} from 'react-toastify';
import { Modal,Button,Form,Spinner } from 'react-bootstrap'
import * as Yup from 'yup'
import { Formik  } from 'formik';
import {InputText,Select} from '../includes/Inputs'
import Loading from '../includes/Loading'
import moment from 'moment';
import styled from 'styled-components'

import { first, values } from 'lodash';
import NumberFormat from 'react-number-format';

const Container = styled.div`
  width: 100%;
  border: 1px solid black;
  margin: 0 auto;
  box-shadow: 10px 10px 18px black;
  padding: 5px;
`
const LabelDay = styled.span`
  position: absolute;
  top: 0;
  right: 0;
  color: #000;
  text-transform: capitalize;
  font-size: 14px
`

const ManualDataFieldValues = (props) => {
    const [loading,setLoading] = useState(false)

    const days = ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab']
    const [dates,setDates] = useState([])
    const [dbValues,setDbValues] = useState([])

    function months(){
        return [
            {id: '01',name:'Enero'},
            {id: '02',name:'Febrero'},
            {id: '03',name:'Marzo'},
            {id: '04',name:'Abril'},
            {id: '05',name:'Mayo'},
            {id: '06',name:'Junio'},
            {id: '07',name:'Julio'},
            {id: '08',name:'Agosto'},
            {id: '09',name:'Septiembre'},
            {id: '10',name:'Octubre'},
            {id: '11',name:'Noviembre'},
            {id: '12',name:'Diciembre'}
        ]
    }

    function years(){
        const year = moment().year()
        const years = []
        for (let index = year - 3; index < year + 3; index++){
            years.push({
                id: index,
                name: index
            })
        }
        return years;
    }

    function fetchDays(month,year){
        
        const result = []
        let week = []

        const firstDate = moment(year+'-'+month+'01',"YYYY-MM-DD")
        const lastDate = moment(year+'-'+month+'-'+moment(year+'-'+month+'01',"YYYY-MM-DD").daysInMonth(),"YYYY-MM-DD")
        
        firstDate.subtract(firstDate.weekday(),'day')
        lastDate.add( 6 - lastDate.weekday(),'day')

        let i = 0
        while (firstDate.diff(lastDate) <= 0 ) {
            i++
            week.push({
                id: firstDate.format('YYYY-MM-DD'),
                label:firstDate.format('DD'),
                render: firstDate.format('MM') == month 
            })
            if(i%7 == 0)
            {
                result.push({
                    id: result.length,
                    days: week
                })
                week= []
            }
            firstDate.add(1,'day')
        }
        return result
    }

    function fetchYears(){
        const result = []
        let week = []
        
        const firstDate = moment( (parseInt(moment().format('YYYY')) - 3) +'-01-01',"YYYY-MM-DD")
        const lastDate =  moment( (parseInt(moment().format('YYYY')) + 2) +'-01-01',"YYYY-MM-DD")
        
        let i = 0
        while (firstDate.diff(lastDate) <= 0 ) {
            i++
            week.push({
                id: firstDate.format('YYYY-MM-DD'),
                label:firstDate.format('YYYY'),
                render: true 
            })
            if(i%3 == 0)
            {
                result.push({
                    id: result.length,
                    days: week
                })
                week= []
            }
            
            firstDate.add(1,'year')
        }
        
        return result
    }

    function fetchMonths(year){
        moment.locale('es')
        const result = [];
        let week = [];

        const firstDate = moment(year+'-01-01',"YYYY-MM-DD")
        const lastDate = moment(year+'-12-01',"YYYY-MM-DD")
        
        let i = 0
        while (firstDate.diff(lastDate) <= 0 ) {
            i++
            week.push({
                id: firstDate.format('YYYY-MM-DD'),
                label:firstDate.format('MMMM'),
                render: true 
            })
            if(i%3 == 0)
            {
                result.push({
                    id: result.length,
                    days: week
                })
                week= []
            }
            firstDate.add(1,'month')
        }
        return result
    }

    return (
        <div>
        
            <Formik
                initialValues={{
                    month: '',
                    year: '',
                    data: {

                    }
                }}
                enableReinitialize={true}
            
                onSubmit={async values  => {  
                    const changes = {}
                    console.log(values)
                    Object.keys(values.data).map(
                        (k)=>{
                            const o = dbValues.find(f=>f.date == k)
                            
                            if(values.data[k] && (o == null || o.field != values.data[k]))
                            {
                                changes[k] = values.data[k]
                            } 
                        }
                    )
                    
                    setLoading(true)
                    const resp = await axios.post(`/api/manual/fields/${props.data.enterpriseId}/${props.data.table}/${props.data.field}/values`,{
                        data: changes,
                        type: props.type
                    })
                    if(resp.statusText == "OK")
                    {
                        toast.success('Los datos se han actualizado correctamente')
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
                            setValues,
                            values
                        })=>{
                        
                            useEffect(() => {
                                resetForm()
                                if (props.show) {
                                    setValues({
                                        year: moment().format("YYYY"),
                                        month: moment().format("MM"),
                                        data: {}
                                    })    
                                }else{
                                    setDates([])
                                }
                            }, [props.show]);

                            useEffect(() => {
                                setDates([])
                                if(!props.show) return ()=>{}
                                let tdates = []
                                switch (props.type) {
                                    case 'day':
                                        if(values.month != '' && values.year != '')   
                                        {
                                            tdates = fetchDays(values.month,values.year)
                                        }     
                                        break;
                                    case 'month':
                                        if(values.year != '')   
                                        {
                                            tdates = fetchMonths(values.year)
                                        }
                                        break;
                                    case 'year':
                                        tdates = fetchYears()
                                        break;
                                }
                                
                                fetchValues(tdates)
                            }, [values.month,values.year]);

                            async function fetchValues(tdates){
                                setDates(tdates)
                                setLoading(true)
                                
                                const data = {};
                                tdates.map(
                                    (week)=>{
                                        week.days.map(
                                            (day)=>{
                                                data[day.id] = ''
                                            }
                                        )
                                    }
                                )
                                

                                const resp = await axios.get(`/api/manual/fields/${props.data.enterpriseId}/${props.data.table}/${props.data.field}/values`,{
                                    params:{
                                        type:props.type,
                                        month: values.month,
                                        year: values.year
                                    }
                                })
                                const respData = resp.data;
                                setDbValues(respData)
                                
                                respData.map(
                                    (dt)=>{
                                        //setFieldValue("data['"+dt.date+"']",dt.field)        
                                        if(dt.field)
                                        {
                                            data[dt.date] = dt.field
                                        }
                                        
                                    }
                                )
                                
                                
                                setFieldValue('data',data)
                                
                                setLoading(false)
                            }

                        return (
                            <Modal show={props.show} size="lg" aria-labelledby="contained-modal-title-vcenter" onHide={props.onHide} className="fade">
                                <Modal.Header closeButton>
                                    <Modal.Title id="contained-modal-title-vcenter">
                                        <i className="fa fa-database"></i>
                                        Entrar valores manuales del campo
                                    </Modal.Title>
                                </Modal.Header>
                                
                                <Modal.Body  >
                                    <Form noValidate  onSubmit={handleSubmit} >
                                        <Loading show={loading}/>
                                        <Form.Row>
                                            
                                            {
                                                props.type == 'day' &&
                                                <Select className="col-6"  name="month" value={values.month} label="Mes" placeholder="Seleccione el mes" onChange={handleChange} >
                                                    {
                                                        months().map(
                                                            (t)=>
                                                                <option key={t.id} value={t.id}>{t.name}</option>
                                                        )
                                                    }
                                                </Select>
                                            }
                                            {
                                                props.type != 'year' &&
                                                <Select className="col-6"  name="year" value={values.year} label="Año" placeholder="Seleccione el año" onChange={handleChange} >
                                                    {
                                                        years().map(
                                                            (t)=>
                                                                <option key={t.id} value={t.id}>{t.name}</option>
                                                        )
                                                    }
                                                </Select>
                                            }
                                        </Form.Row>
                                        
                                        <div style={{ width: '100%' }}>
                                            <Container>
                                                <div>
                                                    <div>
                                                        <table style={{ width: '100%' }}>
                                                            <tbody>
                                                            {
                                                                props.type == 'day' &&
                                                                <tr>
                                                                    {days.map((day) => (
                                                                    <td key={day} style={{ padding: '5px 2px' }}>
                                                                        <div style={{ textAlign: 'center', padding: '5px 0' }}>
                                                                        {day}
                                                                        </div>
                                                                    </td>
                                                                    ))}
                                                                </tr>
                                                            }
                                                            

                                                            {dates.length > 0 && dates.map((week) => (
                                                                <tr key={week.id}>
                                                                {week.days.map((day) => (
                                                                    <td key={day.id} style={{ padding: '1px' }}>
                                                                        {
                                                                            day.render && 
                                                                            <div style={{ textAlign: 'center', padding: '0',fontSize:"11px",position:"relative" }}>
                                                                                <LabelDay>{day.label}</LabelDay>
                                                                                <NumberFormat thousandSeparator="." decimalSeparator="," className="form-control" type="text" name={"data['"+day.id+"']"}  value={values.data[day.id]} onChange={handleChange} 
                                                                                    style={{ padding: '2px',marginBottom:"0",minHeight: props.type == 'day' ? "60px": "80px" }}
                                                                                />
                                                                                
                                                                            </div>
                                                                        }
                                                                    </td>
                                                                ))}
                                                                </tr>
                                                            ))} 
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </Container>
                                        </div>
                                    </Form>
                                </Modal.Body>
                                <Modal.Footer>
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

export default ManualDataFieldValues;