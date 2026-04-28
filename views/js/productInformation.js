let packageObj = {};
let features = {};
let tags = [];
let colors = [];
let sizes = [];
let technicalObject = {};
let editTags = [];
let editColors = { available_colors: [] };
let editSizes = { available_sizes: [] };
let editPackageObj = { box_contents: {} };
let editTechnicalObject = {};
let editFeatures = {};

$(document).ready(function () {
  $("#addBtn").on("click", async function (e) {
    e.preventDefault();
    const { data: dataProducts, error: errorProducts } = await apiRequest(
      "api/products/name",
      {
        method: "GET",
      },
    );
    if (dataProducts) {
      $(`#product_id`).empty();
      let select = getById("product_id");
      dataProducts.data.forEach((product) => {
        let option = new Option(product.name, product.id);
        select.append(option);
      });
    }
    if (errorProducts) {
      notifyErrorResponse(errorProducts);
    }
  });
  $("#editBtn").on("click", function (e) {
    resetEditDynamicData();
  });

  $("#form").on("submit", function (e) {
    e.preventDefault();
    insert();
  });
  $("#form-edit").on("submit", function (e) {
    e.preventDefault();
    update();
  });

  setupDynamicInputs();
  getProductInformation();
  loadEditForm();
  loadDeleteButton();
});

async function insert() {
  const form = $("#form")[0];
  const formData = Object.fromEntries(new FormData(form));

  formData.package_contents = { box_contents: packageObj };
  formData.features = features;
  formData.tags = tags;
  formData.color_options = { available_colors: colors };
  formData.size_options = { available_sizes: sizes };
  formData.technical_details = technicalObject;
  formData.rating_average = parseFloat(formData.rating_average);
  formData.discount = parseFloat(formData.discount);
  formData.is_featured === "on"
    ? (formData.is_featured = true)
    : (formData.is_featured = false);

  const { data, error } = await apiRequest("api/product-information", {
    method: "POST",
    body: JSON.stringify(formData),
  });

  if (data) {
    notifySuccessResponse(API_MSGS.Saved);
    $("#modal-default").modal("hide");
    $("#tableProductInformation").DataTable().ajax.reload(null, false);
    resetDynamicData();
  }

  if (error) notifyErrorResponse(error);
}

async function update() {
  const form = $("#form-edit")[0];
  const formData = Object.fromEntries(new FormData(form));

  formData.features = Object.fromEntries(Object.entries(editFeatures));
  formData.technical_details = Object.fromEntries(
    Object.entries(editTechnicalObject),
  );
  formData.package_contents = {
    box_contents: Object.fromEntries(
      Object.entries(editPackageObj.box_contents || {}),
    ),
  };
  formData.tags = editTags;
  formData.color_options = editColors;
  formData.size_options = editSizes;
  formData.rating_average = parseFloat(formData.rating_average);
  formData.discount = parseFloat(formData.discount);
  formData.is_featured === "on"
    ? (formData.is_featured = true)
    : (formData.is_featured = false);

  const { data, error } = await apiRequest(
    `api/product-information/${parseInt(formData.id)}`,
    {
      method: "PUT",
      body: JSON.stringify(formData),
    },
  );

  if (data) {
    notifySuccessResponse(API_MSGS.Updated);
    $("#modal-edit-default").modal("hide");
    $("#tableProductInformation").DataTable().ajax.reload(null, false);
  }

  if (error) notifyErrorResponse(error);
}

function addPackage() {
  const name = $("#newPackageName").val();
  const value = $("#newPackageValue").val();

  if (!name || !value) return;

  packageObj[name] = value;
  renderPackage();

  $("#newPackageName").val("");
  $("#newPackageValue").val("");
}

function editPackages() {
  const name = $("#editNewPackageName").val();
  const value = $("#editNewPackageValue").val();

  if (!name || !value) return;

  editPackageObj.box_contents[name] = value;

  renderEditPackage();

  $("#editNewPackageName").val("");
  $("#editNewPackageValue").val("");
}

function renderPackage() {
  let html = "";
  for (let key in packageObj) {
    html += `
      <div class="d-flex justify-content-between border p-2 mb-1">
        <span><strong>${key}:</strong> ${packageObj[key]}</span>
        <button type="button" class="btn btn-sm btn-danger" onclick="removePackage('${key}')">×</button>
      </div>
    `;
  }
  $("#packageList").html(html);
}

function removePackage(index) {
  delete packageObj[index];
  renderPackage();
}

function setupDynamicInputs() {
  $("#newTag").on("keypress", function (e) {
    if (e.which === 13) {
      e.preventDefault();
      const value = $(this).val();
      if (!value) return;
      tags.push(value);
      renderTags();
      $(this).val("");
    }
  });

  $("#newColor").on("keypress", function (e) {
    if (e.which === 13) {
      e.preventDefault();
      const value = $(this).val();
      if (!value) return;
      colors.push(value);
      renderColors();
      $(this).val("");
    }
  });

  $("#newSize").on("keypress", function (e) {
    if (e.which === 13) {
      e.preventDefault();
      const value = $(this).val();
      if (!value) return;
      sizes.push(value);
      renderSizes();
      $(this).val("");
    }
  });

  $("#editNewTag").on("keypress", function (e) {
    if (e.which === 13) {
      e.preventDefault();
      const value = $(this).val();
      if (!value) return;
      editTags.push(value);
      renderEditTags();
      $(this).val("");
    }
  });

  $("#editNewColor").on("keypress", function (e) {
    if (e.which === 13) {
      e.preventDefault();
      const value = $(this).val();
      if (!value) return;
      editColors.available_colors.push(value);
      renderEditColors();
      $(this).val("");
    }
  });

  $("#editNewSize").on("keypress", function (e) {
    if (e.which === 13) {
      e.preventDefault();
      const value = $(this).val();
      if (!value) return;
      editSizes.available_sizes.push(value);
      renderEditSizes();
      $(this).val("");
    }
  });
}

function addFeature() {
  const name = $("#newFeatureName").val();
  const value = $("#newFeatureValue").val();

  if (!name || !value) return;

  features[name] = value;
  renderFeatures();

  $("#newFeatureName").val("");
  $("#newFeatureValue").val("");
}

function editFeatureList() {
  const name = $("#editNewFeatureName").val();
  const value = $("#editNewFeatureValue").val();

  if (!name || !value) return;

  editFeatures[name] = value;
  renderEditFeatures();

  $("#editNewFeatureName").val("");
  $("#editNewFeatureValue").val("");
}

function renderFeatures() {
  let html = "";

  for (let key in features) {
    html += `
       <div class="d-flex justify-content-between border p-2 mb-1">
        <span><strong>${key}:</strong> ${features[key]}</span>
        <button type="button" class="btn btn-sm btn-danger" onclick="removeFeature('${key}')">×</button>
      </div>
    `;
  }

  $("#featuresList").html(html);
}

function removeFeature(key) {
  delete features[key];
  renderFeatures();
}

function renderTags() {
  $("#tagsList").html(
    tags.map(
      (t, i) =>
        `<span class="badge badge-info mr-1" onclick="removeTag(${i})" style="cursor:pointer">${t} ×</span>`,
    ),
  );
}

function removeTag(i) {
  tags.splice(i, 1);
  renderTags();
}

function renderColors() {
  $("#colorList").html(
    colors.map(
      (c, i) =>
        `<span class="badge badge-dark mr-1" onclick="removeColor(${i})" style="cursor:pointer">${c} ×</span>`,
    ),
  );
}

function removeColor(i) {
  colors.splice(i, 1);
  renderColors();
}

function renderSizes() {
  $("#sizeList").html(
    sizes.map(
      (s, i) =>
        `<span class="badge badge-secondary mr-1" onclick="removeSize(${i})" style="cursor:pointer">${s} ×</span>`,
    ),
  );
}

function removeSize(i) {
  sizes.splice(i, 1);
  renderSizes();
}

function addTechnical() {
  const key = $("#newTechKey").val();
  const value = $("#newTechValue").val();

  if (!key || !value) return;

  technicalObject[key] = value;
  renderTechnical();

  $("#newTechKey").val("");
  $("#newTechValue").val("");
}

function editTechnicalDetails() {
  const key = $("#editNewTechKey").val();
  const value = $("#editNewTechValue").val();

  if (!key || !value) return;

  editTechnicalObject[key] = value;
  renderEditTechnical();

  $("#editNewTechKey").val("");
  $("#editNewTechValue").val("");
}

function renderTechnical() {
  let html = "";
  Object.entries(technicalObject).forEach(([key, value]) => {
    html += `
      <div class="d-flex justify-content-between border p-2 mb-1">
        <span><strong>${key}:</strong> ${value}</span>
        <button type="button" class="btn btn-sm btn-danger" onclick="removeTechnical('${key}')">×</button>
      </div>
    `;
  });
  $("#technicalList").html(html);
}

function removeTechnical(key) {
  delete technicalObject[key];
  renderTechnical();
}

function resetDynamicData() {
  packageObj = {};
  features = {};
  tags = [];
  colors = [];
  sizes = [];
  technicalObject = {};

  renderPackage();
  renderFeatures();
  renderTags();
  renderColors();
  renderSizes();
  renderTechnical();
}
function resetEditDynamicData() {
  editPackageObj = { box_contents: {} };
  editFeatures = {};
  editTags = [];
  editColors = { available_colors: [] };
  editSizes = { available_sizes: [] };
  editTechnicalObject = {};

  renderEditPackage();
  renderEditFeatures();
  renderEditTags();
  renderEditColors();
  renderEditSizes();
  renderEditTechnical();
}

function getProductInformation() {
  let loaderTimeout;
  let loaderShownAt = null;
  const MIN_VISIBLE_TIME = 400;
  const DEBOUNCE_DELAY = 200;

  $("#tableProductInformation").DataTable({
    serverSide: true,
    processing: false,

    ajax: function (data, callback) {
      loaderTimeout = setTimeout(() => {
        $(".loader").show();
        loaderShownAt = Date.now();
      }, DEBOUNCE_DELAY);

      $.ajax({
        url: "api/product-information/detailed",
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
          console.error("Error loading product information:", error);

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
          const remainingTime = MIN_VISIBLE_TIME - elapsed;

          if (remainingTime > 0) {
            setTimeout(() => {
              $(".loader").hide();
              loaderShownAt = null;
            }, remainingTime);
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
      { data: "product_name" },
      { data: "brand" },
      { data: "manufacturer" },
      { data: "model" },
      { data: "dimensions" },
      { data: "color" },
      { data: "weight" },
      { data: "material" },
      { data: "warranty" },
      { data: "release_date" },
      { data: "expiration_date" },
      { data: "rating_average" },
      { data: "discount" },

      {
        data: "is_featured",
        render: function (data) {
          return data ? "Yes" : "No";
        },
      },

      {
        data: "package_contents",
        render: function (data) {
          return JSON.stringify(data);
        },
      },

      {
        data: "features",
        render: function (data) {
          return JSON.stringify(data);
        },
      },

      {
        data: "tags",
        render: function (data) {
          return JSON.stringify(data);
        },
      },

      {
        data: "color_options",
        render: function (data) {
          return JSON.stringify(data);
        },
      },

      {
        data: "size_options",
        render: function (data) {
          return JSON.stringify(data);
        },
      },

      {
        data: "technical_details",
        render: function (data) {
          return JSON.stringify(data);
        },
      },

      { data: "created_at" },
      { data: "updated_at" },

      getActionsColumnDataTable(),
    ],
  });
}
function loadEditForm() {
  $("#tableProductInformation").on("click", ".edit-button", function () {
    const table = $("#tableProductInformation").DataTable();
    let row = $(this).closest("tr");

    if (row.hasClass("child")) {
      row = row.prev();
    }

    const data = table.row(row).data();

    $("#edit-id").val(data.id);
    $("#edit-brand").val(data.brand);
    $("#edit-manufacturer").val(data.manufacturer);
    $("#edit-model").val(data.model);
    $("#edit-dimensions").val(data.dimensions);
    $("#edit-color").val(data.color);
    $("#edit-weight").val(data.weight);
    $("#edit-material").val(data.material);
    $("#edit-warranty").val(data.warranty);
    $("#edit-releaseDate").val(data.release_date);
    $("#edit-expirationDate").val(data.expiration_date);
    $("#edit-productId").val(data.product_id);

    $("#edit-ratingAverage").val(data.rating_average);
    $("#edit-discount").val(data.discount);
    $("#edit-featuredSwitch").prop("checked", data.is_featured);

    editPackageObj = data.package_contents || { box_contents: {} };
    renderEditPackage();

    if (Array.isArray(data.features)) {
      editFeatures = {};
    } else {
      editFeatures = data.features || {};
    }
    renderEditFeatures();

    editTags = Array.isArray(data.tags) ? data.tags : [];
    renderEditTags();

    editColors = data.color_options || { available_colors: [] };
    if (!Array.isArray(editColors.available_colors)) {
      editColors.available_colors = [];
    }
    renderEditColors();

    editSizes = data.size_options || { available_sizes: [] };
    if (!Array.isArray(editSizes.available_sizes)) {
      editSizes.available_sizes = [];
    }
    renderEditSizes();

    editTechnicalObject = data.technical_details || {};
    renderEditTechnical();

    updateEditHiddenInputs();
  });
}

function updateEditHiddenInputs() {
  $("#edit-packageContentsInput").val(JSON.stringify(editPackageObj));
  $("#edit-featuresInput").val(JSON.stringify(editFeatures));
  $("#edit-tagsInput").val(JSON.stringify(editTags));
  $("#edit-colorOptionsInput").val(JSON.stringify(editColors));
  $("#edit-sizeOptionsInput").val(JSON.stringify(editSizes));
  $("#edit-technicalDetailsInput").val(JSON.stringify(editTechnicalObject));
}

function renderEditPackage() {
  let html = "";

  const contents = editPackageObj.box_contents || {};

  for (let key in contents) {
    html += `
      <div class="d-flex justify-content-between border p-2 mb-1">
        <span><strong>${key}:</strong> ${contents[key]}</span>
        <button type="button" class="btn btn-sm btn-danger" onclick="removeEditPackage('${key}')">×</button>
      </div>
    `;
  }

  $("#editPackageList").html(html);
}

function renderEditFeatures() {
  let html = "";
  if (Array.isArray(editFeatures)) {
    for (let i = 0; i < editFeatures.length; i++) {
      html += `
      <div class="d-flex justify-content-between border p-2 mb-1">
      <span>(${editFeatures[i]})</span>
        <button type="button" class="btn btn-sm btn-danger" onclick="removeEditFeatures('${i}')">×</button>`;
    }
  } else {
    for (let key in editFeatures) {
      html += `
      <div class="d-flex justify-content-between border p-2 mb-1">
        <span>${key} (${editFeatures[key]})</span>
        <button type="button" class="btn btn-sm btn-danger" onclick="removeEditFeatures('${key}')">×</button>
      </div>  
      `;
    }
    $("#editFeaturesList").html(html);
  }
}

function renderEditTags() {
  let html = "";
  if (Array.isArray(editTags)) {
    for (let i = 0; i < editTags.length; i++) {
      html += `
    <span class="badge badge-info mr-1" onclick="removeEditTag('${i}')" style="cursor:pointer">${editTags[i]} ×</span>`;
    }
  } else {
    for (let key in editTags) {
      html += `
    <span class="badge badge-info mr-1" onclick="removeEditTag('${key}')" style="cursor:pointer">${editTags[key]} ×</span>`;
    }
  }
  $("#editTagsList").html(html);
}

function renderEditColors() {
  let html = "";

  editColors.available_colors.forEach((color, i) => {
    html += `
      <span class="badge badge-dark mr-1" onclick="removeEditColor(${i})" style="cursor:pointer">
        ${color} ×
      </span>
    `;
  });

  $("#editColorList").html(html);
}

function renderEditSizes() {
  let html = "";

  editSizes.available_sizes.forEach((size, i) => {
    html += `
      <span class="badge badge-secondary mr-1" onclick="removeEditSize(${i})" style="cursor:pointer">
        ${size} ×
      </span>
    `;
  });

  $("#editSizeList").html(html);
}

function renderEditTechnical() {
  let html = "";
  for (let key in editTechnicalObject) {
    html += `
      <div class="d-flex justify-content-between border p-2 mb-1">
        <span><strong>${key}:</strong> ${editTechnicalObject[key]}</span>
        <button type="button" class="btn btn-sm btn-danger" onclick="removeEditTechnical('${key}')">×</button>
      </div>
    `;
  }
  $("#editTechnicalList").html(html);
}

function removeEditPackage(key) {
  delete editPackageObj.box_contents[key];
  renderEditPackage();
}

function removeEditFeatures(key) {
  delete editFeatures[key];
  renderEditFeatures();
}

function removeEditTag(key) {
  editTags.splice(key, 1);
  renderEditTags();
}

function removeEditColor(i) {
  editColors.available_colors.splice(i, 1);
  renderEditColors();
}

function removeEditSize(i) {
  editSizes.available_sizes.splice(i, 1);
  renderEditSizes();
}

function removeEditTechnical(key) {
  delete editTechnicalObject[key];
  renderEditTechnical();
}

function loadDeleteButton() {
  document.addEventListener("click", async function (e) {
    let btn = e.target.closest('[id^="btn-delete_"]');
    if (!btn) return;

    let id = btn.id.split("_")[1];
    deleteInformation(id);
  });
}

async function deleteInformation(id) {
  const response = await getDeleteMsg();
  if (response.isConfirmed) {
    const { data, error } = await apiRequest(`api/product-information/${id}`, {
      method: "DELETE",
    });

    if (data) {
      notifySuccessResponse(API_MSGS.Deleted);
      $("#tableProductInformation").DataTable().ajax.reload(null, false);
    }
    if (error) {
      notifyErrorResponse(error);
    }
  }
}
