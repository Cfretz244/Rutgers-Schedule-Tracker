require 'net/http'
require 'uri'
require 'json'
require 'twilio-ruby'
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

heatherCourses = [Course.new('GOVT', 111, 0)]
begin
    while true
        for course in heatherCourses
            postParams = {
            'lookopt' => 'DEP',
            'term' => '201460',
            'param1' => course.dep,
            'param2' => 'U',
            'openopt' => 'ALL'
            }
            uri = URI.parse 'http://max.millersville.edu/prod/hwzkschd.P_MU_SchedDisplay'
            response = Net::HTTP.post_form(uri, postParams)

        end
    end
rescue
    puts "error"
end
