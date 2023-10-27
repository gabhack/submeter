import React, { Component  } from 'react';
import { Modal,Button } from 'react-bootstrap'
import { render, unmountComponentAtNode } from 'react-dom'
import { css } from "@emotion/core";
import "./Confirmation.css";

class Confirmation extends Component{

    constructor(props) 
    {    
        super(props);
        this.state = {
            show: true,
            title:'',
            body:''
        }
    }

    onClose(){
        this.setState({show:false}) 
    }

    render(){
        
        return (
        <Modal show={this.state.show} size="sm" aria-labelledby="contained-modal-title-vcenter" onHide={(e)=>{this.onClose(e)}} centered className="fade modal-confirmation">
            <Modal.Header closeButton>
              <Modal.Title id="contained-modal-title-vcenter">
                {this.props.header}
              </Modal.Title>
            </Modal.Header>
            <Modal.Body  >
              {this.props.body}
            </Modal.Body>
            <Modal.Footer>
              <Button variant="secondary"  size="sm" onClick={(e)=>{this.onClose(e)} }>
                  <i className="fa fa-times"></i>
                  Cerrar
              </Button>
              <Button variant="primary" size="sm" onClick={(e)=>{this.onClose(e);this.props.onConfirm(e)}}>
                  <i className="fa fa-check"></i>
                  Aceptar
              </Button>
            </Modal.Footer>
        </Modal>
        );
    } 
}

function createElementReconfirm (properties) {
    let divTarget = document.getElementById('confirm-alert')
    if (divTarget) {
        divTarget.parentNode.removeChild(divTarget);
    } 
    divTarget = document.createElement('div')
    divTarget.id = 'confirm-alert'
    document.body.appendChild(divTarget)
    render(<Confirmation {...properties} />, divTarget)
  }

export function confirmation (properties) {
    createElementReconfirm(properties)
}