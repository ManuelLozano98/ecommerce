$(document).ready(function () {
  const orderDiv = getById("order");
  const trackingNumber = orderDiv.dataset.id;
  if (trackingNumber) {
    trackOrder(trackingNumber);
  }
});

function trackOrder(number) {
  fetch(`${BASE_URL}/my-orders/track/${number}`, {
    headers: {
      "X-Requested-With": "XMLHttpRequest",
      Accept: "application/json",
    },
    method: "GET",
  })
    .then((res) => res.json())
    .then((data) => {
      if (data) {
        let etaFormatted = "";
        if (data.eta) {
          const date = new Date(data.eta);
          etaFormatted = date.toLocaleDateString("en-US", {
            month: "short",
            day: "numeric",
            year: "numeric",
          });
        }
        getById("order").innerHTML = `
        <div class="card bg-success" style="max-width:300px">
              <div class="card-header">
                <h3 class="card-title">Details</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                </div>
                <!-- /.card-tools -->
              </div>
              <!-- /.card-header -->
              <div class="card-body" style="display: block;">
               <p><strong>Tracking Number:</strong> ${data.tracking_number}</p>
        <p><strong>Carrier:</strong> ${data.carrier}</p>
        <p><strong>Status:</strong> ${data.status}</p>
        <p><strong>Status Details:</strong> ${data.status_details}</p>
        <p><strong>Estimated Time of Arrival:</strong> ${etaFormatted ?? "N/A"}</p>
              </div>
              <!-- /.card-body -->
            </div>`;
      }
      if (data.message) {
        notifyErrorResponse(data);
      }
    })
    .catch((err) => console.error(err));
}
