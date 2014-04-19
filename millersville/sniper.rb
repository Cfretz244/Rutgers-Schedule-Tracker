require 'net/http'
require 'uri'
require 'json'
require 'nokogiri'
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

class String
    def is_integer?
        self.to_i.to_s == self
    end
end

account_sid = "AC3c4e47084e170e028847ee3dbfef6cd0"
auth_token = "10c79fad2ebb7911cb8c1f9c2a5f1ad8"
client = Twilio::REST::Client.new account_sid, auth_token

heatherCourses = [Course.new('GOVT', 111, 0), Course.new('GOVT', 111, 1)]
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
            rawResponse = Net::HTTP.post_form(uri, postParams)
            resp = Nokogiri::HTML rawResponse.body
            status = nil
            column = 0
            resp.css('tr').each do |row|
                if (row['class'] == 'even-row' or row['class'] == 'odd-row') and row.children.size > 2
                    column = 0
                    target = false
                    name = nil
                    row.children.each do |child|
                        if (child.children.size > 0 or child.text != "\n") and not child['colspan']
                            stripped = child.text.strip.delete "\n"
                            if stripped and stripped != ''
                                if column == 1 and stripped.include?(course.dep) and stripped.include?(course.course.to_s)
                                    if course.sec and stripped.split(' ')[2].to_i == course.sec
                                        target = true
                                    elsif not course.sec
                                        target = true
                                    end
                                end
                                if column == 3
                                    name = stripped
                                end
                                if column == 9 and stripped.is_integer? and stripped.to_i > 0 and target
                                    course.sec ? message = "#{name}, section ##{course.sec} is open!" : message = "#{name} is open!"
                                    client.account.sms.messages.create(:body => message, :to => '6108125436', :from => '2674332999')
                                end
                                column += 1
                            end
                        end
                    end
                end
            end
        end
        sleep 30
    end
rescue
    client.account.sms.messages.create(:body => 'Millersville script has crashed', :to => '2152370055', :from => '2674332999')
end
