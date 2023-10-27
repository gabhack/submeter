import React, { Component, useEffect, useState } from "react";
import ReactDOM from "react-dom";

import axios from "axios";

import { toast, ToastContainer } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";
import { Formik } from "formik";
import * as Yup from "yup";
import { Form, Modal, Button, Col, Table, Row, Card } from "react-bootstrap";
import { InputText, Select, InputColor } from "../includes/Inputs";
import StatisticConfigField from "./StatisticConfigField";
import Datatable from "../includes/Datatable";

const StatisticConfig = (props) => {
  const [enterprices, setEnterprices] = useState([]);
  const [meters, setMeters] = useState([]);

  const [databaseMeta, setDatabaseMeta] = useState([]);
  const [loading, setLoading] = useState(false);
  const [currentField, setCurrentField] = useState({
    show: false,
    data: {},
  });
  const [backUrl, setBackUrl] = useState("");
  const [enterpriceId, setEnterpriceId] = useState(0);
  const [productionTypes, setProductionTypes] = useState([]);
  /* functions */

  async function fetchEnterprices() {
    const res = await axios.get(props.baseUrl + "/api/enterprices", {});
    setEnterprices(res.data);
  }

  async function fetchMeters(enterprise_id) {
    const res = await axios.get(
      props.baseUrl + "/api/enterprices/" + enterprise_id + "/meters",
      {}
    );
    setMeters(res.data);
  }

  function fetchDatabaseMeta(meta_id) {
    axios.get(props.baseUrl + "/produccion/connections/" + meta_id, {}).then(
      (res) => {
        setDatabaseMeta(res.data);
      },
      (err) => {
        setDatabaseMeta([]);
      }
    );
  }

  function fetchProductionTypes(meta_id) {
    axios.get(props.baseUrl + "/api/production_types", {}).then(
      (res) => {
        setProductionTypes(res.data);
      },
      (err) => {
        setProductionTypes([]);
      }
    );
  }

  function insertField() {
    setCurrentField({
      show: true,
      data: {},
    });
  }

  function getCurrentEnterprice() {
    const urlParams = new URLSearchParams(window.location.search);
    const enterpriceId = urlParams.get("emp_id");

    if (enterpriceId) {
      setEnterpriceId(enterpriceId);
      setBackUrl(props.baseUrl + "/empresa/" + enterpriceId);
    } else {
      setEnterpriceId(0);
      setBackUrl(props.backUrl);
    }

    return enterpriceId;
  }

  return (
    <div className="banner col-md-12 mb-4 mr-3">
      {props.configId == 0 ? (
        <h3>
          <i className="fa fa-plus-square"></i>
          Crear Configuración
        </h3>
      ) : (
        <h3>
          <i className="fa fa-check-square"></i>
          Modificar Configuración
        </h3>
      )}

      <hr></hr>

      <Formik
        initialValues={{
          id: 0,
          name: "",
          enterprise_id: 0,
          meter_id: 0,
          color: "",
          chart_type: "",
          chart_interval_daily: "",
          chart_interval_weekly: "",
          fields: [],
        }}
        enableReinitialize
        validationSchema={Yup.object().shape({
          name: Yup.string().required("Este campo es obligatorio"),
          enterprise_id: Yup.string().required("Este campo es obligatorio"),
          meter_id: Yup.string().required("Este campo es obligatorio"),
          color: Yup.string().required("Este campo es obligatorio"),
          chart_type: Yup.string().required("Este campo es obligatorio"),
          chart_interval_daily: Yup.string().required(
            "Este campo es obligatorio"
          ),
          chart_interval_weekly: Yup.string().required(
            "Este campo es obligatorio"
          ),
        })}
        onSubmit={async (values) => {
          if (values.id == 0) {
            values.type = props.type;
            let res = await axios.post(`/api/statics/configs`, values);
            if (res.status == 200) {
              toast.success("La configuracion se ha creado correctamente");
              setTimeout(() => {
                window.location.href = backUrl;
              }, 100);
            } else if (res.status != 399) {
              toast.error(res.data);
            }
          } else {
            values._method = "PUT";
            let res = await axios.post(
              `/api/statics/configs/${values.id}`,
              values
            );
            if (res.status == 200) {
              toast.success("La configuracion se ha modificado correctamente");
              setTimeout(() => {
                window.location.href = backUrl;
              }, 100);
            } else if (res.status != 399) {
              toast.error(res.data);
            }
          }
        }}
      >
        {({
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
          nextState,
        }) => {
          useEffect(() => {
            const enterpriceId = getCurrentEnterprice();
            resetForm();
            fetchEnterprices();
            fetchProductionTypes();

            if (props.configId != 0) {
              fetchData(props.configId);
            } else {
              fetchCurrentEnterprice(enterpriceId, "");
            }
          }, [props.configId]);

          async function fetchData(id, enterpriceId) {
            const res = await axios.get(
              props.baseUrl + `/api/statics/configs/${id}`,
              {}
            );
            const data = res.data;

            setValues({
              id: data.id ? data.id : 0,
              name: data.name ? data.name : "",

              color: data.color ? data.color : "",
              chart_type: data.chart_type ? data.chart_type : "",
              chart_interval_daily: data.chart_interval_daily
                ? data.chart_interval_daily
                : "",
              chart_interval_weekly: data.chart_interval_weekly
                ? data.chart_interval_weekly
                : "",
              fields: data.fields ? data.fields : [],
            });

            fetchCurrentEnterprice(data.enterprise_id, data.meter_id);
          }

          async function fetchCurrentEnterprice(enterprise_id, meter_id) {
            if (enterprise_id) {
              setFieldValue(
                "enterprise_id",
                enterprise_id ? enterprise_id : ""
              );
              await fetchMeters(enterprise_id);
              setFieldValue("meter_id", meter_id ? meter_id : "");
              if (meter_id) fetchDatabaseMeta(meter_id ? meter_id : "");
            }
          }

          function onSaveField(data) {
            const fields = Object.assign([], values.fields);

            if (data.id == 0) {
              //Insert
              let id = 0;
              fields.map((f) => {
                if (f.id > id) id = f.id;
              });
              data.id = id + 1;
              fields.push(data);
            } else {
              //Update
              const index = fields.findIndex((f) => f.id == data.id);
              fields[index] = data;
            }
            setFieldValue("fields", fields);
            setCurrentField({ show: false });
          }

          return (
            <Form noValidate onSubmit={handleSubmit}>
              <div className="row mb-3">
                <div className="col-sm-6">
                  <a
                    className="btn btn-primary text-white float-left"
                    href={backUrl}
                  >
                    <i className="fa fa-undo"></i>
                    Regresar
                  </a>
                </div>
                <div className="col-sm-6">
                  <button className="btn btn-success text-white float-right">
                    <i className="fa fa-check"></i>
                    Guardar
                  </button>
                </div>
              </div>
              <Form.Group>
                <Form.Row>
                  <InputText
                    className="col-4"
                    type="text"
                    name="name"
                    value={values.name}
                    label="Nombre"
                    placeholder="Entre el nombre de la configuración"
                    onChange={handleChange}
                  />

                  <Select
                    disabled={enterpriceId != 0}
                    className="col-4"
                    name="enterprise_id"
                    value={values.enterprise_id}
                    label="Empresa"
                    placeholder="Seleccione la empresa"
                    onChange={(e) => {
                      handleChange(e);
                      fetchMeters(e.target.value);
                    }}
                  >
                    {enterprices &&
                      enterprices.map((en) => (
                        <option key={en.id} value={en.id}>
                          {en.name}
                        </option>
                      ))}
                  </Select>
                  <Select
                    className="col-4"
                    name="meter_id"
                    value={values.meter_id}
                    label="Contador"
                    placeholder="Seleccione el contador"
                    onChange={(e) => {
                      handleChange(e);
                      fetchDatabaseMeta(e.target.value);
                    }}
                  >
                    {meters &&
                      meters.map((mt) => (
                        <option key={mt.id} value={mt.id}>
                          {mt.name}
                        </option>
                      ))}
                  </Select>
                </Form.Row>
                <Form.Row>
                  <InputColor
                    className="col-2"
                    name="color"
                    value={values.color}
                    label="Color"
                    onChange={handleChange}
                  />
                  <Select
                    className="col-4"
                    name="chart_type"
                    value={values.chart_type}
                    label="Tipo de gráfica"
                    placeholder="Seleccione el tipo de gráfica"
                    onChange={handleChange}
                  >
                    <option value="line">Línea</option>
                    <option value="bar">Barra</option>
                    <option value="area">Area</option>
                    <option value="pie">Pie</option>
                    <option value="column">Columna</option>
                  </Select>
                  <Select
                    className="col-3"
                    name="chart_interval_daily"
                    value={values.chart_interval_daily}
                    label="Intervalo diario"
                    placeholder="Seleccione el intervalo diario(minutos)"
                    onChange={handleChange}
                  >
                    <option value="15">15 minutos</option>
                    <option value="30">30 minutos</option>
                    <option value="45">45 minutos</option>
                    <option value="60">1 Hora</option>
                  </Select>
                  <Select
                    className="col-3"
                    name="chart_interval_weekly"
                    value={values.chart_interval_weekly}
                    label="Intervalo semanal"
                    placeholder="Seleccione el intervalo semanal(minutos)"
                    onChange={handleChange}
                  >
                    <option value="15">15 minutos</option>
                    <option value="30">30 minutos</option>
                    <option value="45">45 minutos</option>
                    <option value="60">1 Hora</option>
                    <option value="120">2 Horas</option>
                    <option value="240">4 Horas</option>
                    <option value="360">6 Horas</option>
                    <option value="720">12 Horas</option>
                    <option value="1440">24 Horas</option>
                  </Select>
                </Form.Row>
                <Form.Row>
                  <div className="col-6">
                    <h4>
                      <i className="fa fa-tags"></i>
                      Campos agrupadores
                    </h4>
                  </div>
                  <div className="col-6">
                    <Button
                      variant="outline-primary"
                      className="float-right"
                      onClick={insertField}
                    >
                      <i className="fa fa-plus-square"></i>
                      Insertar
                    </Button>
                  </div>
                  <hr></hr>
                  <div className="col-12">
                    <div className="mt-2">
                      <Datatable
                        data={values.fields}
                        className="table table-striped table-responsive bg-white mt-3"
                        columns={[
                          {
                            title: "",
                            data: null,
                            width: 20,
                            createdCell: (td, cellData, rowData) => {
                              let txt = "";

                              return ReactDOM.render(
                                <div
                                  style={{
                                    backgroundColor: rowData.color,
                                    width: "20px",
                                    height: "20px",
                                  }}
                                ></div>,
                                td
                              );
                            },
                          },
                          {
                            title: "Name",
                            data: "name",
                          },
                          {
                            title: "Nombre mostrado",
                            data: "display_name",
                          },
                          {
                            title: "Tipo",
                            data: "field_type_name",
                          },

                          // {
                          //     title: "Tipo",
                          //     data:null,
                          //     createdCell: (td, cellData, rowData) => {
                          //         let txt = ''//props.productTypeName(rowData.field_type);

                          //         console.log(productionTypes)
                          //         console.log(props.productionTypes)
                          //         /*if(props.productionTypes)
                          //         {
                          //             const c = props.productionTypes.find(f=>f.id == rowData.field_type)
                          //             if(c) txt = c.name
                          //         }
                          //         */

                          //         return ReactDOM.render(
                          //             <div>{txt}</div>
                          //             ,td
                          //         )
                          //     }
                          // },
                          {
                            title: "Operacion",
                            data: null,
                            createdCell: (td, cellData, rowData) => {
                              let txt = "";

                              switch (rowData.operation_type) {
                                case "1":
                                  txt = "Suma total";
                                  break;
                                case "2":
                                  txt = "Promedio";
                                  break;
                                case "3":
                                  txt = "Mediana";
                                  break;
                                case "4":
                                  txt = "Min";
                                  break;
                                case "5":
                                  txt = "Max";
                                  break;
                                case "6":
                                  txt = "Desviación estandar";
                              }

                              return ReactDOM.render(<div>{txt}</div>, td);
                            },
                          },
                          {
                            title: "Editar",
                            data: null,
                            searchable: false,
                            sortable: false,
                            width: 50,
                            action: {
                              className: "btn btn-primary",
                              icon: "fa fa-pen",
                              event: "update",
                            },
                          },
                          {
                            title: "Remover",
                            data: null,
                            searchable: false,
                            sortable: false,
                            width: 50,
                            action: {
                              className:
                                "btn btn-danger btn-delete-assign-energy",
                              icon: "fa fa-times",
                              event: "remove",
                            },
                          },
                        ]}
                        onUpdate={(row) => {
                          setCurrentField({
                            show: true,
                            data: row,
                          });
                        }}
                        onRemove={(row) => {
                          setFieldValue(
                            "fields",
                            values.fields.filter((f) => f.id != row.id)
                          );
                        }}
                      >
                        <thead className="bg-submeter-4">
                          <tr>
                            <th className="text-white"></th>
                            <th className="text-white">Nombre</th>
                            <th className="text-white">Nombre mostrado</th>
                            <th className="text-white">Tipo</th>
                            <th className="text-white">Operacion</th>
                            <th className="text-white">Editar</th>
                            <th className="text-white">Remover</th>
                          </tr>
                        </thead>
                        <tbody></tbody>
                      </Datatable>
                    </div>
                  </div>
                </Form.Row>
              </Form.Group>
              <StatisticConfigField
                productionTypes={productionTypes}
                databaseMeta={databaseMeta ? databaseMeta : []}
                show={currentField.show}
                data={currentField.data}
                onHide={() => setCurrentField({ show: false })}
                onSave={onSaveField}
              />
            </Form>
          );
        }}
      </Formik>

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
  );
};

if (document.getElementById("statistic-config-frm")) {
  var doc = document.getElementById("statistic-config-frm");

  ReactDOM.render(
    <StatisticConfig
      configId={doc.getAttribute("data-id")}
      type={doc.getAttribute("data-type")}
      baseUrl={doc.getAttribute("data-base-url")}
      backUrl={doc.getAttribute("data-back-url")}
    />,
    doc
  );
}
