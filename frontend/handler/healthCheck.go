package handler

import (
	"encoding/json"
	"fmt"
	"io/ioutil"
	"net/http"

	"github.com/gianarb/shopmany/frontend/config"
)

const unhealthy = "unhealthy"
const healthy = "healthy"

type HealthCheckResponse struct {
	Status string 	`json:"status"`
	Checks []Check 	`json:"checks"`
}

type Check struct {
	Name 		string 	`json:"name"`
	Status		string 	`json:"status"`
	Error       string  `json:"error"`
}

type getHealthCheckHandler struct {
	config  config.Config
	hclient *http.Client
}

func NewHealthCheckHandler(config config.Config, hclient *http.Client) *getHealthCheckHandler {
	return &getHealthCheckHandler{
		config:  config,
		hclient: hclient,
	}
}

func (h *getHealthCheckHandler) ServeHTTP(w http.ResponseWriter, r *http.Request) {
	// ctx := r.Context()
	w.Header().Add("Content-Type", "application/json")
	healthCheck := HealthCheckResponse{
		Status: unhealthy,
		Checks: []Check{},
	}

	itemCheck := checkItem(h.config.ItemHost, h.hclient)
	
	if itemCheck.Status == healthy {
		healthCheck.Status = healthy
	}
	healthCheck.Checks = append(healthCheck.Checks, itemCheck)
	

	body, err := json.Marshal(healthCheck)
	if err != nil {
		w.WriteHeader(500)
	}
	if healthCheck.Status == unhealthy {
		w.WriteHeader(500)
	}
	fmt.Fprintf(w, string(body))
}


func checkItem(host string, hclient *http.Client) Check {
	check := Check {
		Name: "item",
		Status: unhealthy,
		Error: "",
	} 

	req, err := http.NewRequest("GET", fmt.Sprintf("%s/health", host), nil)
	resp, err := hclient.Do(req)
	if err != nil {
		check.Error = err.Error()
		return check
	}
	defer resp.Body.Close()
	body, err := ioutil.ReadAll(resp.Body)
	if err != nil {
		check.Error = err.Error()
		return check
	}
	ch := HealthCheckResponse{
		Status: unhealthy,
	}
	err = json.Unmarshal(body, &ch)
	if err == nil && resp.StatusCode >= 200 && resp.StatusCode < 300 && ch.Status == healthy {
		check.Status = healthy
		return check
	}
	
	check.Error = string(body)
	return check
}