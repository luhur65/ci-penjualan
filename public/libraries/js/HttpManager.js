class HttpManager {
  static initInterceptor(apiUrl, getAccessToken, logoutCallback, refreshUrl) {
    this.apiUrl = apiUrl;
    this.getAccessToken = getAccessToken;
    this.logoutCallback = logoutCallback;
    this.refreshUrl = refreshUrl;
    this.isRefreshing = false;
    this.refreshSubscribers = [];

    $.ajaxPrefilter(function(options, originalOptions, jqXHR) {
      let originalError = options.error;
      options.error = function(jqXHR, textStatus, errorThrown) {
        if (jqXHR.status === 401) {
          console.log("Intercept 401, refresh token jalan...");
          HttpManager.handleTokenExpired(originalOptions);
          return;
        }
        if (typeof originalError === "function") {
          originalError(jqXHR, textStatus, errorThrown);
        }
      };
    });

    $.ajaxSetup({
      beforeSend: function(xhr) {
        let token = HttpManager.getAccessToken();
        if(token) xhr.setRequestHeader('Authorization', `Bearer ${token}`);
      },
      statusCode: {
        422: function(error) {
          if ($('#crudForm').length > 0 && !$('#crudForm').is(":hidden")) {
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
            if(typeof setErrorMessages === 'function') setErrorMessages($('#crudForm'), error.responseJSON.errors);
          }
        }
      },
      error: function(jqXHR, textStatus, errorThrown) {
        if (textStatus === 'timeout') {
          console.error(new Error('Request timeout', 'ajax-setup'));
        } else if (textStatus === 'error' && jqXHR.status === 0) {
          console.error(new Error('Network error', 'ajax-setup'));
        } else {
          console.error(`Error AJAX: ${textStatus} - ${errorThrown}`);
        }
      }
    });
  }

  static refreshAccessToken() {
    return $.ajax({
      url: this.refreshUrl,
      type: "get",
      dataType: "json",
      success: function(response) {
        // Will be handled dynamically, but for fallback:
        if (typeof ACCESS_TOKEN !== 'undefined') ACCESS_TOKEN = response.access_token;
      },
      error: function(xhr, status, error) {
        console.log("Gagal memperbarui token:", error);
        if (xhr.status === 401 && typeof HttpManager.logoutCallback === 'function') {
          HttpManager.logoutCallback();
        }
      }
    });
  }

  static handleTokenExpired(req) {
    return new Promise((resolve, reject) => {
      this.refreshSubscribers.push((newToken) => {
        req.headers = req.headers || {};
        req.headers['Authorization'] = `Bearer ${newToken}`;
        $.ajax(req).done(resolve).fail(reject);
      });

      if (!this.isRefreshing) {
        this.isRefreshing = true;
        this.refreshAccessToken()
          .done((newToken) => {
            this.isRefreshing = false;
            let tokenString = newToken.access_token;
            if (typeof ACCESS_TOKEN !== 'undefined') ACCESS_TOKEN = tokenString;
            this.refreshSubscribers.forEach(cb => cb(tokenString));
            this.refreshSubscribers = [];
          })
          .fail(() => {
            this.isRefreshing = false;
            if (typeof this.logoutCallback === 'function') this.logoutCallback();
          });
      }
    });
  }

  static ajaxWithRefresh(options, slowThreshold = 2000) {
    const start = performance.now();
    return $.ajax(options)
      .then(response => {
        const duration = performance.now() - start;
        if (duration > slowThreshold) {
          console.warn(`Request lambat: ${duration.toFixed(0)} ms`, options.url);
        }
        return response;
      })
      .catch(error => {
        const duration = performance.now() - start;
        if (duration > slowThreshold) {
          console.warn(`Request lambat (error): ${duration.toFixed(0)} ms`, options.url);
        }
        if (error.status === 401) {
          return HttpManager.handleTokenExpired(options);
        }
        return Promise.reject(error);
      });
  }
}