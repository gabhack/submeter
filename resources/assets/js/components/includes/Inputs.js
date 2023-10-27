import React, { Component  } from 'react';
import { Form,Col } from 'react-bootstrap'
import { ErrorMessage,Field  } from 'formik';
import { css } from "@emotion/core";
import MultiSelect from "react-multi-select-component";
import { isString } from 'lodash';
import NumberFormat from 'react-number-format';

const style = {
    color: "#d02323",
    fontSize: "13px"
}

class InputText extends Component{

    constructor(props) 
    {    
        super(props);    
        this.state = {

        }
    }

    render(){
        
        return (
            <Form.Group className={this.props.className} style={this.props.style}>
                <Form.Label>{this.props.label}</Form.Label>
                <Form.Control type={this.props.type} placeholder={this.props.placeholder} name={this.props.name} 
                    value={this.props.value}
                    onChange={this.props.onChange}
                    readOnly={this.props.readOnly}
                />
                <ErrorMessage  style={style} component="div" className="error" name={this.props.name} />
            </Form.Group>
        );
    }
    /*
    componentDidMount(){
       
    }

    componentWillUnmount(){
        
    }

    shouldComponentUpdate(nextProps, nextState){
     
    }

    componentDidUpdate(nextProps) {
        
    } */
}

class InputArea extends Component{

    constructor(props) 
    {    
        super(props);    
        this.state = {

        }
    }

    render(){
        
        return (
            <Form.Group className={this.props.className}>
                <Form.Label>{this.props.label}</Form.Label>
                <Form.Control as="textarea" spellCheck="false" rows={this.props.rows ? this.props.rows : 3 } placeholder={this.props.placeholder} name={this.props.name} 
                    value={this.props.value}
                    onChange={this.props.onChange}
                />
                <ErrorMessage  style={style} component="div" className="error" name={this.props.name} />
            </Form.Group>
        );
    }
    /*
    componentDidMount(){
       
    }

    componentWillUnmount(){
        
    }

    shouldComponentUpdate(nextProps, nextState){
     
    }

    componentDidUpdate(nextProps) {
        
    } */
}

class Select extends Component{

    constructor(props) 
    {    
        super(props);    
        this.state = {

        }
    }

    render(){

        return (
            <Form.Group className={this.props.className} style={this.props.style}>
                {this.props.label && <Form.Label>{this.props.label}</Form.Label>}
                <Form.Control disabled={this.props.disabled} as="select" placeholder={this.props.placeholder} name={this.props.name} value={this.props.value}
                    onChange={this.props.onChange}>
                    {
                        this.props.placeholder && <option value="">{this.props.placeholder}</option>
                    }
                    {
                        this.props.children
                    }
                </Form.Control>
                <ErrorMessage  style={style} component="div" className="error" name={this.props.name} />
            </Form.Group>
        );
    }
    /*
    componentDidMount(){
       
    }

    componentWillUnmount(){
        
    }
    
    shouldComponentUpdate(nextProps, nextState){
     
    }
    
    componentDidUpdate(nextProps) {
      if(nextProps.children != this.props.children){
          
          this.render()
      }
    }
    */ 
}

class InputMultiselect extends Component{

    constructor(props) 
    {    
        super(props);    
        this.state = {
            value: []
        }
    }

    render(){
        
        return (
            <Form.Group className={this.props.className} style={this.props.style}>
                
                {this.props.label && <Form.Label>{this.props.label}</Form.Label>}
                <MultiSelect 
                    options={this.props.options}
                    value={this.state.value}
                    onChange={(o)=>{
                        const v = []
                        o.map((t)=>v.push(t.value))
                        this.setState({
                            value: o
                        })
                        this.props.onChange(this.props.name,v)
                    }}
                    name={this.props.name}

                    overrideStrings={
                        {
                            "selectSomeItems": "Seleccionar",
                            "allItemsAreSelected": "Todos los rubros.",
                            "selectAll": "Todos los rubros",
                            "search": "Búscar",
                            "clearSearch": "Limpiar búsqueda"
                        }
                    }
                    
                />
               
                <ErrorMessage  style={style} component="div" className="error" name={this.props.name} />
            </Form.Group>
        );
    }
    /*
    componentDidMount(){
       
    }

    componentWillUnmount(){
        
    }
    
    shouldComponentUpdate(nextProps, nextState){
     
    }
    */
    componentDidUpdate(nextProps) {
      if(nextProps.options != this.props.options){
        let values = [];
        if(isString(this.props.value)) 
            values.push(this.props.value);
        else
            values = this.props.value;
        var filtered = this.props.options.filter(f=>values.includes(f.value))
        this.setState({
            value: filtered     
        })
      }
    }
     
}

class Radio extends Component{

    constructor(props) 
    {    
        super(props);    
        this.state = {

        }
    }

    render(){
        
        return (
            <div className="form-check">
                <label className="form-check-label">
                    <Field type="radio" name={this.props.name} value={this.props.value} />
                    {this.props.label}
                </label>
            </div>
        );
    }
    /*
    componentDidMount(){
       
    }

    componentWillUnmount(){
        
    }

    shouldComponentUpdate(nextProps, nextState){
     
    }

    componentDidUpdate(nextProps) {
        
    } */
}


class RadioGroup extends Component{

    constructor(props) 
    {    
        super(props);    
        this.state = {

        }
    }

    render(){
        
        return (
            <Form.Group className={this.props.className}>
                <Form.Label>{this.props.label}</Form.Label>
                <Form.Group>
                    {this.props.children}
                </Form.Group>
                <ErrorMessage  style={style} component="div" className="error" name={this.props.name} />
            </Form.Group>
        );
    }
    /*
    componentDidMount(){
       
    }

    componentWillUnmount(){
        
    }

    shouldComponentUpdate(nextProps, nextState){
     
    }

    componentDidUpdate(nextProps) {
        
    } */
}


class InputColor extends Component{

    constructor(props) 
    {    
        super(props);    
        this.state = {

        }
    }

    render(){
        
        return (
            <Form.Group className={this.props.className}>
                <Form.Label>{this.props.label}</Form.Label>
                <Form.Control type="color" placeholder={this.props.placeholder} name={this.props.name} 
                    value={this.props.value}
                    onChange={this.props.onChange}
                    style={
                        {
                            height: "38px"
                        }
                    }
                />
                <ErrorMessage  style={style} component="div" className="error" name={this.props.name} />
            </Form.Group>
        );
    }
   
}

class InputNumber extends Component{

    constructor(props) 
    {    
        super(props);    
        this.state = {

        }
    }

    render(){
        
        return (
            <Form.Group className={this.props.className} style={this.props.style}>
                <Form.Label>{this.props.label}</Form.Label>
                <NumberFormat  placeholder={this.props.placeholder} name={this.props.name} 
                    value={this.props.value}
                    onChange={this.props.onChange}
                    readOnly={this.props.readOnly}
                />
                <ErrorMessage  style={style} component="div" className="error" name={this.props.name} />
            </Form.Group>
        );
    }
    
}
export {
    InputText,
    Select,
    RadioGroup,
    Radio,
    InputColor,
    InputArea,
    InputMultiselect,
    InputNumber
}