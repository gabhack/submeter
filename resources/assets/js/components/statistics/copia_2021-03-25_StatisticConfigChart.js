import React, { Component,useEffect,useState  } from 'react';
import ReactDOM from 'react-dom';
import ClockLoader from "react-spinners/ClockLoader";
import axios from 'axios';
import { css } from "@emotion/core";
import { toast,ToastContainer } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';

import jsPDF from 'jspdf';
import 'html2canvas';
import 'jspdf-autotable'

import CanvasJSReact from '../includes/libs/canvasjs.react';
import moment from 'moment';

import * as FileSaver from 'file-saver';
import * as XLSX from 'xlsx';
import { parseInt } from 'lodash';

var CanvasJSChart = CanvasJSReact.CanvasJSChart;

const StatisticConfigChart = (props) => {
    const ref = React.createRef();

    const [options, setOptions] = useState({});
    const [totals, setTotals] = useState([]);
    const [details, setDetails] = useState({});
    const [csv, setCsv] = useState([]);

    const [error,setError] = useState('')

    const [loading, setLoading] = useState(true);
    const [loadingPdf,setLoadingPdf] = useState(false);

    async function exportPdf(){
        try {
            setLoadingPdf(true)
            //options.exportEnabled =  false
            //setOptions(options)
            const html2CanvasOpts = {
                scrollX: -window.scrollX,
                scrollY: -window.scrollY + 15,
                windowWidth: document.documentElement.offsetWidth ,
                windowHeight: document.documentElement.offsetHeight
            }
            
            
            const pdf = new jsPDF();
            pdf.setTextColor("#212B39");
            pdf.setFontSize(11);
            pdf.text(props.type == 'produccion' ? 'Producción submeter': 'Indicadores energéticos', 105, 15, null, null, "center");
            pdf.addImage("/images/Logo_WEB_Submeter.png", "PNG", 185, 10, 12, 12);

            let canvaChart = await html2canvas(document.querySelector(`#chart_${props.configId} canvas`),html2CanvasOpts);
            const imgData = canvaChart.toDataURL('image/png');    
            const h = parseInt(options.pdfHeight)
            pdf.addImage(imgData, 'PNG',7, 25,197,h )
            
            /*
            let canvaTotals = await html2canvas(document.querySelector(`#totals_${props.configId}`),html2CanvasOpts);
            const imgTotals = canvaTotals.toDataURL('image/png');    
            pdf.addImage(imgTotals, 'PNG',12, 125,185,40)
            */

           pdf.autoTable({ html: `#totals_${props.configId} table`, startY: h + 35,//, useCss: true,
                headStyles: {
                    fillColor: "#004165",
                    textColor: "#fff",
                    lineColor: '#fff',
                    lineWidth: 0.5
                },
                styles:{
                    halign: "center",
                    fillColor: "#E5E5E5"
                }
                /*didParseCell: function (data) {
                    if (data.row.section === 'head' && data.row.index === 0) {
                        if(data.column.index > 0)
                        {
                            if(details.header.fields.length >= data.column.index)
                            {
                                data.cell.styles.fillColor = details.header.fields[data.column.index - 1].color
                            }
                        }
                    }
                }*/
            })

            pdf.autoTable({ html: `#details_${props.configId}`, startY: h + 70,//,useCss: true,
                headStyles: {
                    fillColor: "#fff",
                    textColor: "#000"
                },
                didParseCell: function (data) {
                    if (data.row.section === 'head' && data.row.index === 0) {
                        if(data.column.index > 0)
                        {
                            if(details.header.fields.length >= data.column.index)
                            {
                                data.cell.styles.fillColor = details.header.fields[data.column.index - 1].color
                            }
                        }
                    }
                    if(data.row.index >= details.rows.length )
                    {
                        data.cell.styles.fillColor = "#7F7F7F"
                        data.cell.styles.textColor = "#FFF"
                    }
                    
               }
            })
            
            await pdf.save(defaultFileName('pdf'))
            setTimeout(()=>{
                setLoadingPdf(false)
            },3000)   
        } catch (error) {
            setLoadingPdf(false)
            console.log(error)
        }
    }
    
    async function exportExcel(){

        let objectMaxLength = []
        for (let i = 0; i < csv.totals.length; i++) 
        {
            let keys = Object.keys(csv.totals[i]);
            for (let j = 0; j < keys.length; j++) {
              if (typeof keys[j] == "number") {
                objectMaxLength[j] = 10;
              } else {
                objectMaxLength[j] =
                  objectMaxLength[j] >= keys[j].length
                    ? objectMaxLength[j]
                    : keys[j].length;
              }
            }

            let value = Object.values(csv.totals[i]);
            for (let j = 0; j < value.length; j++) {
              if (typeof value[j] == "number") {
                objectMaxLength[j] = 10;
              } else {
                objectMaxLength[j] =
                  objectMaxLength[j] >= value[j].length
                    ? objectMaxLength[j]
                    : value[j].length;
              }
            }
        }
        const wscols = []
        for (let i = 0; i < objectMaxLength.length; i++) {
            wscols.push({
                width:objectMaxLength[i]
            })
        }
        console.log(wscols)
        const fileType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=UTF-8';
        const ws = XLSX.utils.json_to_sheet(csv.totals,{
            origin: "A2",
            skipHeader: true
        });
        XLSX.utils.sheet_add_json(ws,csv.details,{
            origin: "A8"
        });
        //const ws = XLSX.utils.table_to_sheet;
        ws["!cols"] = wscols;
        const wb = { Sheets: { 'data': ws }, SheetNames: ['data'] };
        const excelBuffer = XLSX.write(wb, { bookType: 'xlsx', type: 'array' });
        const data = new Blob([excelBuffer], {type: fileType});

       

        FileSaver.saveAs(data, defaultFileName('csv'));
    }

    async function fetchData(row){
        try {
            setLoading(true)    
            const res =  await axios.get(`/api/statics/resume/${props.configId}`,{});
            
            res.data.data.chart.exportFileName = defaultFileName('')
            setOptions(res.data.data.chart)
            setTotals(res.data.data.totals)
            setDetails(res.data.data.details)
            setCsv(res.data.data.csv)
            
            setLoading(false)
        } catch (error) {
            setLoading(false)
            
            setOptions({})
            setTotals([])
            setDetails({})
            setCsv([])
            setError('Ha ocurrido un error y no se pudieron cargar los datos de la configuración.'+error)

        }
    }
  

    useEffect(() => {
        fetchData()
    },[]);
    
    function defaultFileName(ext){
        let fname = ''
        if(props.type == 'indicadores') 
            fname = 'Indicadores-'
        else
            fname = 'Produccion Submetering-'
        fname += props.counter + '-'
        fname += '('+moment().format('DD-MM-YYYY')+')'
        if(ext) fname += "."+ext
        return fname
    }
    
    return (
       
        <div className="mb-4 mt-3" >
            
            <div className="card mb-3 my-4">
                <div className="card-body pt-3 pb-1 row ">
                    <h4 className="card-title col-8" style={{fontWeight:"bold"}}>
                        <i className="fa fa-chart-bar" style={{marginRight:"8px"}}></i>
                        {props.configName}
                    </h4>
                    <div className="col-4 text-right">
                        <a className="btn btn-sm btn-outline-default " href={`/estadisticas/configuracion/${props.type}/modificar/${props.configId}`}>
                            <i className="fa fa-edit"></i>
                            Editar
                        </a>
                    </div>
                    
                </div>
            </div>
            {
                (error) && 
                <div className="text-danger">
                    {error}
                </div>
            }
            {
                (loading == true) && 
                <div className="text-center">
                    <ClockLoader
                    css={css`
                        display: block;
                        margin: 0 auto;
                    `}
                    size={50}
                    color={"#123abc"}/>
                </div>
            }
            {
                (loading == false && !error) && 
                <div className="plot-tab mx-0 px-0 production-plot" >
                    <div className="p-2">
                        <div id={`chart_${props.configId}`} className="p-3 pb-3" style={{background:"#fff",border: "1px solid #999",borderRadius: "5px"}}>
                            <CanvasJSChart  options = {options} />
                        </div>
                    </div>
                    <div className="row mt-3 mb-3">
                        <div className="col-6">
                            <button className="btn btn-default float-left color-127" onClick={exportExcel}>
                                <i className="fa fa-file-excel"></i>
                                Exportar datos CSV
                            </button>
                            
                        </div>
                        <div className="col-6">
                            
                            <button onClick={exportPdf} className="btn btn-default float-right color-127 ">
                                
                                {
                                    !loadingPdf && 
                                    <span>
                                        <i className="fa fa-file-pdf"></i> 
                                        Generar pdf
                                    </span> 
                                }
                                {
                                    loadingPdf && 
                                    <span>
                                        <ClockLoader
                                            css={css`
                                                float: left;
                                            `}
                                            color={"#fff"}
                                            size={20}/>
                                            Preparando ...
                                    </span> 
                                }
                            </button>
                        
                            {/* <ReactToPdf targetRef={ref} filename="div-blue.pdf">
                                {({toPdf}) => (
                                    <button onClick={toPdf} className="btn btn-default float-right color-127">
                                        <i className="fa fa-pdf"></i>
                                        Generar pdf
                                    </button>
                                )}
                            </ReactToPdf> */}
                        </div>
                    </div>
                    <div id={`totals_${props.configId}`} className="table-totals" style={{width:"90%",margin:"0 auto"}}>
                        <div className="justify-content-md-center">
                            <table className="table table-bordered table-striped table-light text-center">
                                <thead className="bg-submeter-4">
                                    <tr>
                                        {
                                            totals.length > 0 && totals.map(
                                            (t,i)=> <th key={i} className="text-center text-white" style={{verticalAlign: "middle",color:"white"}}>{t.display_name}</th>
                                            )
                                        }
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                    {
                                        totals.length > 0 && totals.map(
                                        (t,i)=> 
                                        <td key={i} >
                                            <span style={{color:"#B3B3B3"}}>Tipo:{t.field_type_name}</span>
                                            <br/>
                                            <span style={{color:"#B3B3B3"}}>{t.value}</span>
                                        </td>
                                        )
                                    }
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    {
                        details.rows && details.rows.length > 0 && 
                        <div  className="table-detail mt-5" style={{width:"90%",margin:"0 auto"}}>
                            <div className="justify-content-md-center">
                                
                                <table id={`details_${props.configId}`} className="table table-bordered table-sm">
                                    <thead style={{background: '#F5F5F6'}}>
                                        {
                                            
                                            details.header && 
                                            (
                                                <tr>
                                                    <th>
                                                        {details.header.interval}
                                                    </th>
                                                    {
                                                        details.header.fields.length > 0 && details.header.fields.map(
                                                        (t,i)=> <th key={i}  className="text-center" style={{verticalAlign: "middle",background:t.color}}>{t.display_name}</th>
                                                        )
                                                    }
                                                    {/* <th className="text-center" style={{verticalAlign: "middle"}}>Totales</th> */}
                                                </tr>
                                            )
                                        }
                                    </thead>
                                    <tbody>
                                        {
                                            details.rows.map(
                                                (row,i)=>
                                                <tr key={i} >
                                                    <td>{row.interval}</td>
                                                    {
                                                            row.fields.length > 0 && row.fields.map(
                                                                (f,j)=> 
                                                                <td key={j}  className="text-center">
                                                                    {f}
                                                                </td>
                                                            )
                                                        }
                                                    {/* <td>{row.interval_total}</td> */}
                                                </tr>   
                                            )
                                        }
                                        <tr style={{background:"#7F7F7F",color:"#fff"}}>
                                            <td className="" style={{color:"#fff",fontWeight:"bold"}}>Promedio</td>
                                            {
                                                details.totals && 
                                                details.totals.map(
                                                    (val,i)=>
                                                    <td key={i}  className="text-center" style={{color:"#fff",fontWeight:"bold"}}>
                                                        {val}
                                                    </td>
                                                )
                                            }
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    }
                   
                </div>

            }
            
        </div>
    )
}



if (document.querySelectorAll('[data-chart-indicator]').length > 0) {
    const docs = document.querySelectorAll('[data-chart-indicator]')
    docs.forEach(doc => {
        ReactDOM.render(<StatisticConfigChart 
            configId={doc.getAttribute('data-chart-indicator')} 
            type={doc.getAttribute('data-type')} 
            configName={doc.getAttribute('data-chart-indicator-name')} 
            baseUrl={doc.getAttribute('data-base-url')} 
            counter={doc.getAttribute('data-counter-name')} 
            />, doc); 
    });
}
