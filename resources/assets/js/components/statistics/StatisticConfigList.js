import React, { Component, useEffect, useState } from "react";
import ReactDOM from "react-dom";
import Datatable from "../includes/Datatable";
import axios from "axios";
import { toast, ToastContainer } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";
import { confirmation } from "../includes/Confirmation";

const StatisticConfigList = (props) => {
  const [data, setData] = useState([]);
  const [dataLength, setDataLength] = useState(0);
  const [loading, setLoading] = useState(false);
  const [enterpriceUrl, setEnterpriceUrl] = useState("");
  const [isModalVisible, setIsModalVisible] = useState(false);

  /* functions */
  function fetchData(row) {
    const q = {
      params: {
        type: props.type,
      },
    };
    if (props.empId) {
      setEnterpriceUrl("?emp_id=" + props.empId);
      q.params.emp_id = props.empId;
    }
    axios.get(props.baseUrl + `/api/statics/configs`, q).then(
      (res) => {
        const updatedData = res.data.map((item, index) => ({
          ...item,
          name: `${index} ${item.name} `,
        }));
        setData(updatedData);
        setDataLength(updatedData.length);
        console.log("Datos actualizados:", updatedData);
      },
      (err) => {
        setData([]);
        setDataLength(0);
      }
    );
  }
  const setLoadingGlobal = (isLoading) => {
    setLoading(isLoading);
  };
  useEffect(() => {
    window.fetchData = fetchData;
    fetchData();

    window.getLength = () => data.length;
    window.setLoadingGlobal = setLoadingGlobal;

    return () => {
      window.fetchData = undefined;
      window.getLength = undefined;
      window.setLoadingGlobal = undefined;
    };
  }, []);

  const getColumns = () => {
    const baseColumns = [
      {
        title: "Nombre",
        data: "name",
        searchable: false,
        sortable: false,
        render: (data) => {
          const nameParts = data.split(" ");
          const position = nameParts.shift();
          const name = nameParts.join(" ");

          return `<span style="visibility: hidden;">${position}</span> ${name}`;
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
          className: "btn btn-danger btn-delete-assign-energy",
          icon: "fa fa-times",
          event: "remove",
        },
      },
    ];

    if (props.type === "representacion") {
      baseColumns.push(
        {
          title: "Subir",
          data: null,
          searchable: false,
          sortable: false,
          render: (data, type, row, meta) => {
            const isDisabled = meta.row === 0 ? "disabled" : "";
            return `<button class="btn" onclick="window.moveConfig('${props.baseUrl}', ${row.id}, 'up')" ${isDisabled}><i class="fa fa-arrow-up"></i></button>`;
          },
        },
        {
          title: "Bajar",
          data: null,
          searchable: false,
          sortable: false,
          render: (data, type, row, meta) => {
            const isDisabled = meta.row === getLength() - 1 ? "disabled" : "";
            return `<button class="btn" onclick="window.moveConfig('${props.baseUrl}', ${row.id}, 'down')" ${isDisabled}><i class="fa fa-arrow-down"></i></button>`;
          },
        }
      );
    }

    return baseColumns;
  };

  return (
    <div className="banner col-md-12 mb-4 mr-3">
      <style>
        {`
        .button-pressed {
          background-color: #ddd; /* o cualquier estilo que prefieras */
          /* Otros estilos que quieras aplicar al botón cuando se presiona */
        }
      `}
      </style>

      <h4>
        {props.type == "indicadores" ? (
          <span>Indicadores</span>
        ) : (
          <span>Representación de datos</span>
        )}
      </h4>
      <hr></hr>
      <div className="row mb-3">
        <div className="col-sm-6">
          <a
            href={
              props.baseUrl +
              `/estadisticas/configuracion/${props.type}/insertar${enterpriceUrl}`
            }
            className="btn btn-success text-white float-left"
          >
            <i className="fa fa-plus"></i>
            Nueva configuración
          </a>
        </div>
        {props.backUrl && (
          <div className="col-sm-6">
            <a
              className="btn btn-primary text-white float-right"
              href={props.backUrl}
            >
              <i className="fa fa-undo"></i>
              Regresar
            </a>
          </div>
        )}
      </div>
      {loading && (
        <div className="loading-indicator">
          {/* Icono de carga */}
          <i className="fa fa-spinner fa-spin"></i> Cargando...
        </div>
      )}
      <div>
        <Datatable
          data={data}
          className="table table-striped table-responsive bg-white mt-3"
          loading={loading}
          columns={getColumns()}
          onUpdate={(row) => {
            window.location.href =
              props.baseUrl +
              `/estadisticas/configuracion/${props.type}/modificar/${row.id}${enterpriceUrl}`;
          }}
          onRemove={(row) => {
            confirmation({
              header: "Eliminar configuración",
              body: "Seguro que desea eliminar la configuración",
              onConfirm: () => {
                axios
                  .post(props.baseUrl + `/api/statics/configs/${row.id}`, {
                    _method: "DELETE",
                    type: props.type,
                  })
                  .then((res) => {
                    toast.success(
                      "La configuración se ha eliminado correctamente"
                    );
                    fetchData();
                  });
              },
            });
          }}
        >
          <thead className="bg-submeter-4">
            <tr>
              <th className="text-white" width="80%">
                Nombre
              </th>
              <th className="text-white">Editar</th>
              <th className="text-white">Eliminar</th>
              {props.type === "representacion" && (
                <>
                  <th className="text-white">Orden</th>
                  <th className="text-white"></th>
                </>
              )}
            </tr>
          </thead>
          <tbody></tbody>
        </Datatable>
      </div>
      {isModalVisible && <div className="modal">Ordenando gráficas...</div>}

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

const moveConfig = (baseUrl, id, direction) => {
  const buttonId = `button-${id}-${direction}`;
  const button = document.getElementById(buttonId);

  if (button) {
    button.classList.add("button-pressed");
  }

  if (window.setLoadingGlobal) {
    window.setLoadingGlobal(true); // Comienza la carga
  }

  axios
    .put(`${baseUrl}/api/statics/configs/configorder/${id}`, { direction })
    .then(() => {
      if (window.fetchData) {
        window.fetchData();
      }
    })
    .catch((error) => {
      console.error("Error al mover la configuración: ", error);
    })
    .finally(() => {
      if (window.setLoadingGlobal) {
        window.setLoadingGlobal(false); // Termina la carga
      }
      if (button) {
        button.classList.remove("button-pressed");
      }
    });
};

window.moveConfig = moveConfig;

if (document.querySelectorAll("[data-statistic-config-list]").length > 0) {
  const docs = document.querySelectorAll("[data-statistic-config-list]");
  docs.forEach((doc) => {
    ReactDOM.render(
      <StatisticConfigList
        type={doc.getAttribute("data-statistic-config-list")}
        baseUrl={doc.getAttribute("data-base-url")}
        backUrl={doc.getAttribute("data-back-url")}
        empId={doc.getAttribute("data-emp-id")}
      />,
      doc
    );
  });
}
