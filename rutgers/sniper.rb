require "net/http"
require "uri"
require "json"
require "twilio-ruby"
class Course
    attr_accessor :dep, :course, :sec
    def initialize department, course, section = nil
        @dep = department
        @course = course
        @sec = section
    end

    def == another_course
        return false if !another_course.is_a?(self.class)
        @dep == another_course.dep && @course == another_course.course && @sec = another_course.sec
    end
end

account_sid = "AC3c4e47084e170e028847ee3dbfef6cd0"
auth_token = "10c79fad2ebb7911cb8c1f9c2a5f1ad8"
client = Twilio::REST::Client.new account_sid, auth_token

miriamCourses = [Course.new(447, 380, 20)]
chrisCourses = [Course.new(198, 344), Course.new(198, 214, 1), Course.new(198, 214, 2), Course.new(198, 214, 3), Course.new(198, 214, 4)]
begin
    while true
        for k in 0..1
            k == 0 ? courses = miriamCourses : courses = chrisCourses
            for i in 0..courses.length - 1
                chosenCourse = courses[i]
                uri = URI.parse "http://sis.rutgers.edu/soc/courses.json?subject=#{chosenCourse.dep}&semester=92014&campus=NB&level=U"
                rawResponse = Net::HTTP.get_response uri
                response = JSON.parse rawResponse.body
                for course in response
                    if course['courseNumber'].to_i == chosenCourse.course
                        sections = course['sections']
                        for section in sections
                            if chosenCourse.sec && section['number'].to_i == chosenCourse.sec && section['openStatus']
                                message = "Hot diggity! #{course['title']} section #{section['number']} is open! To allow the fastest registration possible, the index number of the course is #{section['index']}. Go! Go! Go!"
                                miriamCourses.include?(chosenCourse) ? to = '8138920100' : to = '2152370055'
                                client.account.sms.messages.create(:body => message, :to => to, :from => '2674332999')
                            elsif !chosenCourse.sec && section['openStatus']
                                message = "Hot diggity! #{course['title']} section #{section['number']} is open! To allow the fastest registration possible, the index number of the course is #{section['index']}. Go! Go! Go!"
                                miriamCourses.include?(chosenCourse) ? to = '8138920100' : to = '2152370055'
                                client.account.sms.messages.create(:body => message, :to => to, :from => '2674332999')
                            end
                        end
                    end
                end
            end
        end
        sleep 30
    end
rescue
    client.account.sms.messages.create(:body => 'Script has crashed', :to => '2152370055', :from => '2674332999')
end
