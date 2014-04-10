require "net/http"
require "uri"
require "json"
require "debugger"; debugger
require "twilio-ruby"
class Course
    attr_accessor :dep, :course, :sec
    def initialize department, course, section = nil
        @dep = department
        @course = course
        @sec = section
    end
end

account_sid = "AC3c4e47084e170e028847ee3dbfef6cd0"

courses = [Course.new(447, 380, 20), Course.new(447, 414, 1)]
for i in 0..courses.length - 1
    chosenCourse = courses[i]
    uri = URI.parse "http://sis.rutgers.edu/soc/courses.json?subject=#{chosenCourse.dep}&semester=92014&campus=NB&level=U"
    rawResponse = Net::HTTP.get_response uri
    response = JSON.parse rawResponse.body
    for course in response
        if course["courseNumber"].to_i == chosenCourse.course
            sections = course["sections"]
            for section in sections
                if chosenCourse.sec && section['number'].to_i == chosenCourse.sec && section['openStatus']
                    puts "Hot diggity! #{course['title']} section #{section['number']} is open! Go! Go! Go!"
                elsif !chosenCourse.sec && section['openStatus']
                    puts "Hot diggity! #{course['title']} is open! Go! Go! Go!"
                end
            end
        end
    end
end
