const API_MSGS = {
  General: "Action completed successfully",
  Created: "New entry created successfully",
  Saved: "Data saved successfully",
  Deleted: "Item deleted successfully",
  Updated: "Changes updated successfully",
};

function getDeleteMsg(textElement = "") {
  return Swal.fire({
    title: "Are you sure?",
    html: `You won't be able to revert this. ${textElement}`,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes, delete it",
  });
}

function getButtonsDataTable() {
  return [
    {
      extend: "copy",
      text: "Copy",
      exportOptions: {
        columns: ":visible:not(.no-export)",
      },
    },
    {
      extend: "csv",
      text: "CSV",
      exportOptions: {
        columns: ":visible:not(.no-export)",
      },
    },
    {
      extend: "excel",
      text: "Excel",
      exportOptions: {
        columns: ":visible:not(.no-export)",
      },
    },
    {
      extend: "pdf",
      text: "PDF",
      exportOptions: {
        columns: ":visible:not(.no-export)",
      },
    },
    { extend: "colvis", text: "Column visibility" },
  ];
}
function getDomStyleDataTable() {
  return (
    "<'row'<'col-md-4'l><'col-md-4 text-center'B><'col-md-4'f>>" + // Up: Select + Buttons + Search
    "<'row'<'col-md-12'tr>>" + // Table
    "<'row'<'col-md-6'i><'col-md-6'p>>" // Down: Info + Pagination
  );
}
function getActionsColumnDataTable() {
  return {
    data: null, // Column generated manually
    title: "Actions",
    orderable: false, // This column does not allow sorting
    searchable: false, // This column does not allow searching
    className: "no-export",
    render: function (data, type, row) {
      let id = Object.values(data)[0];
      return `
            <button id="btn-edit${id}" class="btn btn-success btn-sm rounded-0 edit-button" type="button" data-toggle="modal" data-target="#modal-edit-default" data-placement="top" title="Edit"><i class="fa fa-edit"></i></button>
            <button id="btn-delete_${id}" class="btn btn-danger btn-sm rounded-0" type="button" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash"></i></button>
          `;
    },
  };
}

function getSettingsDataTable() {
  return {
    bDestroy: true,
    scrollCollapse: true,
    autoWidth: false,
    responsive: true,
    paging: true,
  };
}

function closeModalDialog() {
  $(document).on("keydown", function (event) {
    if (event.key === "Escape") {
      $(".modal").modal("hide");
    }
  });
}

function updateCounter(elementToUpdate, counterElement, limitChars) {
  $(counterElement).text($(elementToUpdate).val().length + "/" + limitChars);
  if ($(elementToUpdate).val().length >= limitChars) {
    $(counterElement).addClass("text-danger", "fw-bold");
  } else {
    $(counterElement).removeClass("text-danger", "fw-bold");
  }
}

function notifyErrorResponse(res) {
  let message;
  let details = "";

  try {
    message = res.message || "Request error";

    if (res.details) {
      const detailsObj = res.details;

      for (const key in detailsObj) {
        const fieldErrors = detailsObj[key];
        for (const errorKey in fieldErrors) {
          details += `${fieldErrors[errorKey]}\n`;
        }
      }
    }
  } catch (e) {
    toastr.error("Unexpected server error");
    console.error("Server response: ", res);
    return;
  }

  toastr.error(`Error: ${message} - ${details}`);
}



function notifySuccessResponse(action) {
  toastr.success(action || API_MSGS.General);
}

function loginInvalid() {
  return Swal.fire({
    title: "Invalid login",
    text: "Incorrect username or password",
    icon: "error",
    confirmButtonColor: "#3085d6",
    showCloseButton: true,
  });
}

function getById(elementId) {
  return document.getElementById(elementId);
}

function getJSONForm(form) {
  const dataObj = getForm(form);
  return JSON.stringify(dataObj);
}
function getForm(form) {
  const formData = new FormData(form);
  const dataObj = Object.fromEntries(formData.entries());
  return dataObj;
}

async function apiRequest(url, options = {}) {
  try {
    const response = await fetch(url, {
      headers: {
        "Content-Type": "application/json",
        ...(options.headers || {}),
      },
      ...options,
    });

    const data = await response.json();

    if (!response.ok) {
      throw data;
    }

    return { data, error: null };
  } catch (error) {
    return { data: null, error };
  }
}

function truncateText(data) {
  const truncated = data.length > 30 ? data.substring(0, 30) + "..." : data;
  if (truncated.substring(0, truncated.indexOf("..."))) {
    return `<span>${truncated}</span>
              <button class="btn btn-link btn-sm view-full-text" data-full="${encodeURIComponent(
                data
              )}">See more</button>`;
  } else {
    return `<span>${truncated}</span>`;
  }
}

function checkForm(form) {
  if (!form.checkValidity()) {
    form.reportValidity();
    return false;
  }
  return true;
}

closeModalDialog();
