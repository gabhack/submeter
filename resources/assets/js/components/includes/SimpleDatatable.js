import React, { Component,Fragment  } from 'react';
import ReactDOM from 'react-dom';
import "./Datatable.css";
const $ = require("jquery");
$.DataTable = require("datatables.net-bs4");
import ClockLoader from "react-spinners/ClockLoader";
import { css } from "@emotion/core";
import { v4 as uuidv4 } from 'uuid';

const override = css`
  display: block;
  margin: 0 auto;
`;

class SimpleDatatable extends Component{

    constructor(props) 
    {    
        super(props);
        this.loading = true;        
        this.state = {
            loading: true,
            uuid: uuidv4()
        }
    }

    render(){
        
        return (
            <div>
                
                <table id={this.state.uuid} className={this.props.className + ' '+ ((this.state.loading == true || this.props.loading == true) ? 'loading':'')} width="100%" cellSpacing="0" ref={(el) => (this.el = el)}>
                    {this.props.children}   
                    {(this.state.loading == true || this.props.loading == true) &&
                        <tbody>
                            <tr>
                                <td colSpan="100%">
                                <ClockLoader
                                    css={override}
                                    size={50}
                                    color={"#123abc"}/>
                                </td>
                            </tr>
                        </tbody>
                    }
                    
                </table>
            </div>
        );
    }

    componentDidMount(){
        this.$el = $(this.el);
        
        this.$el.on("draw.dt", function () {
            if($(this).hasClass('loading')) {
                $(this).find(".dataTables_empty").parents('tbody').empty();
                $('#'+$(this).attr('id')+'_info').empty();
                $('#'+$(this).attr('id')+'_paginate').empty();
            }
        })

        this.$el.DataTable({
            //dom: '<"datatabl"t>',
            //data: this.props.data,
            //columns: this.props.columns,
            ordering: true,
            paging: true,
            searching: true,
            pagingType: 'simple',
            initComplete: (settings, json) => {
                this.setState({
                    loading: false
                })
                this.loading = false;
                this.forceUpdate();
            },
            language: {
				"url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
			}
        });
    }

    componentWillUnmount(){
        //this.$el.DataTable().destroy(true);    
    }

    load(data) {   
        
        setTimeout(() => {
            const table = this.$el.DataTable() ; // $('.data-table-wrapper').find('table').DataTable();
            //console.log(table);
            table.clear();
            table.rows.add(data);     
            table.draw();      
        }, 1);
    }

    /*shouldComponentUpdate(nextProps, nextState){
       
        return false;    
    }*/

    /*shouldComponentUpdate(nextProps,prevProps) {
        
        // if(nextProps.data != prevProps.data ){
        //    //console.log(this.state.uuid)
        //    //console.log(nextProps.data);
        //    this.load(nextProps.data);              
        // }
        
        // return false;
        if(nextProps.loading != this.loading ){
            this.loading = nextProps.loading;
            //this.props.loading = nextProps.loading;
            setTimeout(() => {
                this.setState({
                    loading:nextProps.loading
                })    
            }, 1);
        }
    }*/ 
}

export default SimpleDatatable;