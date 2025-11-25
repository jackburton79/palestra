import * as api from "./api.js";
import { state } from "./state.js";
import * as ui from "./ui.js";
import { showError } from "./utils.js";

async function loadWorkouts() {
  try {
    const data = await api.getWorkouts();
    // assuming API returns { workouts: [...] }
    state.workouts = data.workouts;
    ui.renderWorkoutsList();
  } catch (err) {
    // error already shown by api.request
  }
}

async function openWorkout(wk) {
  state.currentWorkout = wk;
  try {
    const sessionData = await api.getWorkoutSession(wk.id);
    state.session = sessionData;
    showSection("session-section");
    ui.renderSession();
  } catch (err) {
    // error handled above
  }
}

async function deleteWorkout(wk) {
  if (!confirm(`Delete workout "${wk.name}"?`)) return;
  try {
    await api.deleteWorkout(wk.id);
    // reload list
    await loadWorkouts();
  } catch (err) {}
}

function showSection(sectionId) {
  document.getElementById("workouts-section").hidden = sectionId !== "workouts-section";
  document.getElementById("session-section").hidden = sectionId !== "session-section";
}

function setupEventListeners() {
  window.addEventListener("openWorkout", (e) => {
    openWorkout(e.detail);
  });

  window.addEventListener("deleteWorkout", (e) => {
    deleteWorkout(e.detail);
  });

  document.getElementById("back-to-workouts-btn").addEventListener("click", () => {
    showSection("workouts-section");
    state.currentWorkout = null;
    state.session = null;
  });

  document.getElementById("add-workout-btn").addEventListener("click", async () => {
    const name = prompt("Workout name:");
    if (name) {
      try {
        await api.createWorkout(name);
        await loadWorkouts();
      } catch (err) {
        // error shown
      }
    }
  });
}

async function init() {
  setupEventListeners();
  await loadWorkouts();
  showSection("workouts-section");
}

document.addEventListener("DOMContentLoaded", init);
