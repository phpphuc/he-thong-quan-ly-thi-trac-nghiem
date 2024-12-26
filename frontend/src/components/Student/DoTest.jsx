

const DoTest = () => {
  return (
    <div className="w-full h-full max-w-4xl mx-auto mt-8 bg-gray-100 px-10 py-5 font-nunito">
      {/* Header */}
      <div className="flex items-center justify-between mb-8">
        <div>
          <h1 className="text-2xl font-bold mb-4">Bài thi môn <span>OOP</span></h1>
        </div>
        <div className="flex items-center justify-center">
          <div className="text-blue-600 font-bold mr-12">Thời gian: <span>40:00</span></div>
          <div className="mr-6">
            <button className="w-28 bg-blue-500 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300">Nộp bài</button>
          </div>
          <div>
            <button className="w-28 bg-blue-500 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300">Báo lỗi</button>
          </div>
        </div>
      </div>
      
      {/* Nội dung bài thi */}
      <div className="overflow-x-auto max-h-[500px] bg-white rounded-2xl">
        <div className="px-12 py-6">
          {/* Các câu hỏi và câu trả lời */}
          <div className="mb-6">
            <div>
              <h2 className="font-semibold mb-4">
                <span>1</span>
                <span>. Hãy cho biết khái niệm OOP là gì?</span>
              </h2>
            </div>
            <div>
              <div className="mb-3 flex items-center">
                <input type="radio" name="id__answer--1"/> 
                <span className="ml-2">A. OOP là object oriented programming.</span>
              </div>
              <div className="mb-3 flex items-center">
                <input type="radio" name="id__answer--1"/>
                <span className="ml-2">B. OOP là object oriented programming.</span>
              </div>
              <div className="mb-3 flex items-center">
                <input type="radio" name="id__answer--1"/>
                <span className="ml-2">C. OOP là object oriented programming.</span>
              </div>
              <div className="mb-3 flex items-center">
                <input type="radio" name="id__answer--1"/>
                <span className="ml-2">D. OOP là object oriented programming.</span>
              </div>
            </div>
          </div>

          <div className="mb-6">
            <div>
              <h2 className="font-semibold mb-4">
                <span>2</span>
                <span>. Hãy cho biết khái niệm OOP là gì?</span>
              </h2>
            </div>
            <div>
              <div className="mb-3 flex items-center">
                <input type="radio" name="id__answer--2"/> 
                <span className="ml-2">A. OOP là object oriented programming.</span>
              </div>
              <div className="mb-3 flex items-center">
                <input type="radio" name="id__answer--2"/>
                <span className="ml-2">B. OOP là object oriented programming.</span>
              </div>
              <div className="mb-3 flex items-center">
                <input type="radio" name="id__answer--2"/>
                <span className="ml-2">C. OOP là object oriented programming.</span>
              </div>
              <div className="mb-3 flex items-center">
                <input type="radio" name="id__answer--2"/>
                <span className="ml-2">D. OOP là object oriented programming.</span>
              </div>
            </div>
          </div>

          <div className="mb-6">
            <div>
              <h2 className="font-semibold mb-4">
                <span>3</span>
                <span>. Hãy cho biết khái niệm OOP là gì?</span>
              </h2>
            </div>
            <div>
              <div className="mb-3 flex items-center">
                <input type="radio" name="id__answer--3"/> 
                <span className="ml-2">A. OOP là object oriented programming.</span>
              </div>
              <div className="mb-3 flex items-center">
                <input type="radio" name="id__answer--3"/>
                <span className="ml-2">B. OOP là object oriented programming.</span>
              </div>
              <div className="mb-3 flex items-center">
                <input type="radio" name="id__answer--3"/>
                <span className="ml-2">C. OOP là object oriented programming.</span>
              </div>
              <div className="mb-3 flex items-center">
                <input type="radio" name="id__answer--3"/>
                <span className="ml-2">D. OOP là object oriented programming.</span>
              </div>
            </div>
          </div>

          <div className="mb-6">
            <div>
              <h2 className="font-semibold mb-4">
                <span>4</span>
                <span>. Hãy cho biết khái niệm OOP là gì?</span>
              </h2>
            </div>
            <div>
              <div className="mb-3 flex items-center">
                <input type="radio" name="id__answer--4"/> 
                <span className="ml-2">A. OOP là object oriented programming.</span>
              </div>
              <div className="mb-3 flex items-center">
                <input type="radio" name="id__answer--4"/>
                <span className="ml-2">B. OOP là object oriented programming.</span>
              </div>
              <div className="mb-3 flex items-center">
                <input type="radio" name="id__answer--4"/>
                <span className="ml-2">C. OOP là object oriented programming.</span>
              </div>
              <div className="mb-3 flex items-center">
                <input type="radio" name="id__answer--4"/>
                <span className="ml-2">D. OOP là object oriented programming.</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}

export default DoTest