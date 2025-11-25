import { showLoading, showError, clearError } from "./utils.js";

import { API_BASE } from "./config.js";

async function request(path, options = {}) {
  showLoading(true);
  clearError();
  try {
    const res = await fetch(`${API_BASE}${path}`, {
      headers: { "Content-Type": "application/json" },
      ...options,
    });
    const data = await res.json();

    if (!res.ok) {
      throw new Error(data.error || `API error: ${res.status}`);
    }
    return data;
  } catch (err) {
    showError(err.message);
    console.error("API request failed:", err);
    throw err;
  } finally {
    showLoading(false);
  }
}

// Example API functions, adjust according to your API
export async function getWorkouts() {
  return request("workouts", { method: "GET" });
}

export async function getWorkoutSession(workoutId) {
  return request("getSession", {
    method: "POST",
    body: JSON.stringify({ workout_id: workoutId }),
  });
}

export async function createWorkout(name) {
  return request("workout", {
    method: "POST",
    body: JSON.stringify({ name }),
  });
}

export async function deleteWorkout(workoutId) {
  return request("workout", {
    method: "DELETE",
    body: JSON.stringify({ workout_id: workoutId }),
  });
}

// Add more functions: addExercise, updateSession, etc.
