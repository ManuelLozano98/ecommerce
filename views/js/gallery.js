$(document).ready(function () {
  getGallery();

  $("#form").on("submit", function (e) {
    e.preventDefault();
    const form = getById("form");
    if (checkForm(form)) {
      insert();
    }
  });

  $("#form-edit").on("submit", function (e) {
    e.preventDefault();
    const form = getById("form-edit");
    if (checkForm(form)) {
      edit();
    }
  });

  $("#addBtn").on("click", function () {
    loadProducts("productSelect");
  });

  getById("image").addEventListener("change", function (e) {
    const file = e.target.files[0];
    if (!file) return;

    const preview = getById("preview");
    preview.src = URL.createObjectURL(file);
  });

  getById("edit-image").addEventListener("change", function (e) {
    const file = e.target.files[0];
    if (!file) return;

    const preview = getById("edit-preview");
    preview.src = URL.createObjectURL(file);
  });

  loadEditForm();
  loadDeleteButton();
});

function getGallery() {
  let loaderTimeout;
  let loaderShownAt = null;

  const MIN_VISIBLE_TIME = 400;
  const DEBOUNCE_DELAY = 200;

  $("#tableGallery").DataTable({
    serverSide: true,
    processing: false,

    ajax: function (data, callback) {
      loaderTimeout = setTimeout(() => {
        $(".loader").show();
        loaderShownAt = Date.now();
      }, DEBOUNCE_DELAY);

      $.ajax({
        url: "api/gallery/detailed",
        method: "GET",
        data: data,

        success: function (response) {
          callback({
            draw: response.draw,
            recordsTotal: response.recordsTotal,
            recordsFiltered: response.recordsFiltered,
            data: response.data,
          });
        },

        error: function (xhr, status, error) {
          console.error("Error loading gallery:", error);

          callback({
            draw: data.draw,
            recordsTotal: 0,
            recordsFiltered: 0,
            data: [],
          });

          toastr.error(error);
        },

        complete: function () {
          clearTimeout(loaderTimeout);

          if (!loaderShownAt) return;

          const elapsed = Date.now() - loaderShownAt;
          const remaining = MIN_VISIBLE_TIME - elapsed;

          if (remaining > 0) {
            setTimeout(() => {
              $(".loader").hide();
              loaderShownAt = null;
            }, remaining);
          } else {
            $(".loader").hide();
            loaderShownAt = null;
          }
        },
      });
    },

    ...getSettingsDataTable(),

    buttons: getButtonsDataTable(),
    dom: getDomStyleDataTable(),

    columns: [
      { data: "id" },

      {
        data: "product_name",
      },

      {
        data: "image",
        render: function (data) {
          if (!data) return "No image";

          return `<img src="uploads/images/${data}" width="100" height="100" style="object-fit:cover;">`;
        },
      },

      {
        data: "type",
        render: function (data) {
          return data;
        },
      },

      {
        data: "active",
        render: function (data) {
          return data === 1 ? "Yes" : "No";
        },
      },

      { data: "created_at" },
      { data: "updated_at" },

      getActionsColumnDataTable(),
    ],
  });
}

async function loadProducts(selectId, selected = "") {
  $(`#${selectId}`).empty();

  const { data, error } = await apiRequest(`api/products/name`, {
    method: "GET",
  });

  if (data) {
    data.data.forEach((product) => {
      let option = new Option(product.name, product.id);

      if (selected && selected === product.id) {
        option.selected = true;
      }

      $(`#${selectId}`).append(option);
    });
  }

  if (error) {
    notifyErrorResponse(error);
  }
}

async function insert() {
  const form = new FormData(getById("form"));

  let active = form.get("active");

  if (active === "on") {
    form.set("active", "1");
  } else {
    form.set("active", "0");
  }

  fetch("api/gallery", {
    method: "POST",
    body: form,
  })
    .then((res) => res.json())
    .then((data) => {
      if (data?.status === "success") {
        notifySuccessResponse(API_MSGS.Created);
      } else {
        notifyErrorResponse(data);
      }
      $("#modal-default").modal("hide");
      getDatatable("tableGallery").ajax.reload(null, false);
    })
    .catch((err) => notifyErrorResponse(err));
}

function loadEditForm() {
  $("#tableGallery").on("click", ".edit-button", function () {
    const table = $("#tableGallery").DataTable();

    let row = $(this).closest("tr");

    if (row.hasClass("child")) {
      row = row.prev();
    }

    const data = table.row(row).data();

    $("#imageId").val(data.id);

    loadProducts("edit-productSelect", data.product_id);

    $("#edit-type").val(data.type);

    if (data.image) {
      $("#edit-preview").attr("src", "uploads/images/" + data.image);
      $("#edit-preview").show();
    } else {
      $("#edit-preview").hide();
    }

    $("#edit-active").prop("checked", data.active === 1);
  });
}

async function edit() {
  const form = new FormData(getById("form-edit"));

  let active = form.get("active");

  if (active === "on") {
    form.set("active", "1");
  } else {
    form.set("active", "0");
  }

  const id = form.get("id");

  fetch(`api/gallery/${id}`, {
    method: "POST",
    body: form,
  })
    .then((res) => res.json())
    .then((data) => {
      if (data?.status === "success") {
        notifySuccessResponse(API_MSGS.Updated);
      } else {
        notifyErrorResponse(data);
      }
      $("#modal-edit-default").modal("hide");
      getDatatable("tableGallery").ajax.reload(null, false);
    })
    .catch((err) => notifyErrorResponse(err));
}

function loadDeleteButton() {
  document.addEventListener("click", function (e) {
    let deleteBtn = e.target.closest('[id^="btn-delete_"]');

    if (deleteBtn) {
      let id = deleteBtn.id.split("_")[1];

      deleteItem(id);
    }
  });
}

async function deleteItem(id) {
  const response = await getDeleteMsg();

  if (response.isConfirmed) {
    const { data, error } = await apiRequest(`api/gallery/${id}`, {
      method: "DELETE",
    });

    if (data) {
      notifySuccessResponse(API_MSGS.Deleted);

      getDatatable("tableGallery").ajax.reload(null, false);
    }

    if (error) {
      notifyErrorResponse(error);
    }
  }
}
