import { Routes, Route } from "react-router-dom";
// import Login from "./components/pages/LoginPage/Login";
import NotFound from "./components/pages/NotFoundPage/NotFound";
import LookUpDetails from "./components/Student/LookUpDetails";

function App() {
  return (
    <Routes>
     {/* <Route path="/" element={<Login />}></Route> */}

     <Route path="/" element={<LookUpDetails />}></Route>

     {/* More routing here ...*/}

     <Route path="*" element={<NotFound />}></Route>
    </Routes>
  )
}

export default App
